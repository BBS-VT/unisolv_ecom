<?php

namespace App\Console\Commands;

use App\Models\Customer;
use App\Models\CustomerCategory;
use App\Models\Order;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Generates fictional dealers and back-dated sales orders against the EXISTING
 * products, for demos. Products, product photos and media are never touched.
 *
 * Everything it creates is tagged so it can be removed again:
 *   - orders:             InternalComments = 'DEMO-SEED'
 *   - stock transactions: notes start with 'DEMO-SEED'
 *   - customers:          StoreEAN starts with 'DEMOSEED'
 *
 *   php artisan demo:seed-sales --dry-run     (build everything, then roll back)
 *   php artisan demo:seed-sales               (write it)
 *   php artisan demo:seed-sales --purge       (remove it, restoring stock)
 */
class DemoSeedSales extends Command
{
    protected $signature = 'demo:seed-sales
        {--months=6 : Months of sales history to generate}
        {--per-month=25 : Approximate orders per month}
        {--customers=12 : Number of fictional dealer accounts to create}
        {--company= : Company id (defaults to the company that owns the products)}
        {--dry-run : Build everything, report, then roll back}
        {--purge : Remove all previously seeded demo data and restore stock}';

    protected $description = 'Generate (or remove) fictional demo sales orders against the existing products';

    private const TAG = 'DEMO-SEED';
    private const EAN_PREFIX = 'DEMOSEED';

    public function handle(): int
    {
        if (app()->environment('production')) {
            $this->error('Refusing to run: APP_ENV is production. This command is for demo sites only.');
            return self::FAILURE;
        }

        DB::beginTransaction();

        try {
            $this->option('purge') ? $this->purge() : $this->seed();

            if ($this->option('dry-run')) {
                DB::rollBack();
                $this->warn('Dry run - nothing was saved.');
            } else {
                DB::commit();
            }
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->error($e->getMessage());
            return self::FAILURE;
        }

        return self::SUCCESS;
    }

    private function seed(): void
    {
        if (DB::table('orders')->where('InternalComments', self::TAG)->exists()) {
            throw new \RuntimeException('Demo orders already exist. Run with --purge first.');
        }

        $companyId = $this->option('company')
            ?: DB::table('products')->whereNotNull('company_id')->value('company_id');
        if (!$companyId) {
            throw new \RuntimeException('Could not work out the company id. Pass --company=ID.');
        }

        $reps = DB::table('users')->where('IsSalesperson', 1)->pluck('id')->all()
            ?: [DB::table('users')->orderBy('id')->value('id')];

        // ---- Products: active, in stock, priced; prefer ones that have a photo ----
        $base = DB::table('products as p')
            ->join('stock_item_holdings as h', 'h.StockCode', '=', 'p.StockCode')
            ->where('p.company_id', $companyId)
            ->where('p.status', 1)
            ->whereNull('p.deleted_at')
            ->where('p.SellingPrice', '>', 0)
            ->where('h.QuantityOnHand', '>=', 20)
            ->select('p.StockCode', 'p.StockItemName', 'p.SellingPrice', 'p.SellingPrice2',
                'p.SellingPrice3', 'p.SellingPrice4', 'h.LocationCode', 'h.QuantityOnHand');

        $morph = (new Product)->getMorphClass();
        $withPhotos = (clone $base)->whereExists(function ($q) use ($morph) {
            $q->select(DB::raw(1))->from('media')
                ->whereColumn('media.model_id', 'p.id')
                ->where('media.model_type', $morph);
        })->get();

        $products = $withPhotos->count() >= 30 ? $withPhotos : $base->get();
        if ($withPhotos->count() < 30) {
            $this->warn("Only {$withPhotos->count()} in-stock products have photos; using all in-stock products instead.");
        }

        // One holding row per product (the one with the most stock), then a shuffled pool of 150.
        $products = $products->sortByDesc('QuantityOnHand')->unique('StockCode')
            ->shuffle()->take(150)->values()->all();
        if (count($products) < 5) {
            throw new \RuntimeException('Fewer than 5 eligible products (active, priced, 20+ in stock). Nothing to sell.');
        }

        // Pareto-ish popularity: first few products sell much more often.
        $prodWeights = [];
        foreach ($products as $i => $p) {
            $prodWeights[$i] = 1 / pow($i + 1, 0.8);
        }

        $onHand = [];   // running stock per StockCode
        $budget = [];   // never sell more than 60% of starting stock, so nothing goes out of stock
        foreach ($products as $p) {
            $onHand[$p->StockCode] = (float) $p->QuantityOnHand;
            $budget[$p->StockCode] = (int) floor($p->QuantityOnHand * 0.6);
        }

        // ---- Customers ----
        $customers = $this->createCustomers((int) $this->option('customers'), $companyId, $reps[0]);
        $custWeights = [];
        foreach ($customers as $i => $c) {
            $custWeights[$i] = 1 / pow($i + 1, 0.7);
        }

        // ---- Order dates ----
        $stamps = $this->orderTimestamps((int) $this->option('months'), (int) $this->option('per-month'));

        // Order numbers: sequential, oldest first. (Order::getNextOrderNumber() sorts by created_at,
        // which breaks with back-dated rows, so we number them ourselves.)
        $orderNo = 1 + (int) DB::table('orders')->max(DB::raw('CAST(OrderNumber AS UNSIGNED)'));

        $stats = ['orders' => 0, 'lines' => 0, 'revenue' => 0, 'stock_tx' => 0, 'status' => []];
        $touched = [];

        foreach ($stamps as $ts) {
            $cust = $customers[$this->pick($custWeights)];
            $rep = $reps[array_rand($reps)];
            $collection = mt_rand(1, 100) <= 30;
            $status = $this->statusFor($ts);
            if ($collection && in_array($status, [3, 7])) $status = 6;
            if (!$collection && $status === 6) $status = 7;
            $deducts = $status !== 9;

            // Build lines
            $lines = [];
            $wanted = [1, 1, 2, 2, 2, 3, 3, 3, 4, 4, 5, 6, 8][array_rand([1, 1, 2, 2, 2, 3, 3, 3, 4, 4, 5, 6, 8])];
            for ($attempt = 0; $attempt < $wanted * 4 && count($lines) < $wanted; $attempt++) {
                $p = $products[$this->pick($prodWeights)];
                if (isset($lines[$p->StockCode])) continue;

                $qty = [1, 1, 2, 2, 3, 4, 5, 6, 10, 12][array_rand([1, 1, 2, 2, 3, 4, 5, 6, 10, 12])];
                if ($deducts) $qty = min($qty, $budget[$p->StockCode]);
                if ($qty < 1) continue;

                $col = match ((int) $cust->price_level) {
                    2 => 'SellingPrice2', 3 => 'SellingPrice3', 4 => 'SellingPrice4', default => 'SellingPrice',
                };
                $price = (float) ($p->$col ?: $p->SellingPrice);
                $unit = (int) round($price * 100);   // cents, VAT-inclusive (same as checkout)

                $lines[$p->StockCode] = ['p' => $p, 'qty' => $qty, 'unit' => $unit, 'total' => $unit * $qty];
                if ($deducts) $budget[$p->StockCode] -= $qty;
            }
            if (!$lines) continue;

            $total = array_sum(array_column($lines, 'total'));
            $subTotal = (int) round($total / 1.15);   // checkout: VAT 15%, prices include VAT

            $picked = in_array($status, [3, 4, 6, 7, 8]);
            $order = Order::create([
                'company_id' => $companyId,
                'CustomerID' => $cust->acc_code,
                'SalesPersonID' => $rep,
                'PickedByPersonID' => $picked ? $rep : null,
                'OrderStatusID' => $status,
                'Authorisation' => 0,
                'OrderNumber' => sprintf('%06d', $orderNo++),
                'OrderDate' => $ts->toDateString(),
                'ExpectedDeliveryDate' => $collection ? null : $ts->copy()->addDays(rand(2, 4))->toDateString(),
                'CustomerPurchaseOrderNumber' => mt_rand(1, 100) <= 70 ? 'PO-' . rand(1000, 9999) : null,
                'tax_per_item' => false,
                'discount_per_item' => false,
                'discount_type' => 'percent',
                'discount_val' => 0,
                'sub_total' => $subTotal,
                'total' => $total,
                'InternalComments' => self::TAG,
                'PickingCompletedWhen' => $picked ? min($ts->copy()->addHours(3), now()) : null,
                'delivery_method' => $collection ? 'collection' : 'delivery',
                'preferred_delivery_date' => $collection ? null : $ts->copy()->addDays(rand(2, 5))->toDateString(),
                'LastEditedBy' => $rep,
                'created_at' => $ts,
                'updated_at' => $ts,
            ]);

            foreach ($lines as $code => $l) {
                $loc = $l['p']->LocationCode;
                DB::table('orders_items')->insert([
                    'OrderID' => $order->id,
                    'company_id' => $companyId,
                    'StockItem' => $code,
                    'LocationCode' => $loc,
                    'discount_type' => 'percent',
                    'Quantity' => $l['qty'],
                    'discount_val' => 0,
                    'UnitPrice' => $l['unit'],
                    'total' => $l['total'],
                    'PickedQuantity' => $picked ? $l['qty'] : null,
                    'PickingCompletedWhen' => $picked ? min($ts->copy()->addHours(3), now()) : null,
                    'LastEditedBy' => $rep,
                    'created_at' => $ts,
                    'updated_at' => $ts,
                ]);

                if ($deducts) {
                    $before = $onHand[$code];
                    $after = $before - $l['qty'];
                    DB::table('stock_transactions')->insert([
                        'StockCode' => $code,
                        'LocationCode' => $loc,
                        'transaction_type' => 'order',
                        'quantity_change' => -$l['qty'],
                        'quantity_before' => $before,
                        'quantity_after' => $after,
                        'reference_type' => 'Order',
                        'reference_id' => $order->id,
                        'notes' => self::TAG . " order #{$order->OrderNumber}",
                        'user_id' => $rep,
                        'company_id' => $companyId,
                        'created_at' => $ts,
                        'updated_at' => $ts,
                    ]);
                    $onHand[$code] = $after;
                    $touched[$code] = $loc;
                    $stats['stock_tx']++;
                }
                $stats['lines']++;
            }

            $this->writeStatusHistory($order->id, $status, $collection, $ts, $rep);

            $stats['orders']++;
            $stats['revenue'] += $total;
            $stats['status'][$status] = ($stats['status'][$status] ?? 0) + 1;
        }

        // Apply the final stock levels in one update per product.
        foreach ($touched as $code => $loc) {
            DB::table('stock_item_holdings')
                ->where('StockCode', $code)->where('LocationCode', $loc)
                ->update(['QuantityOnHand' => $onHand[$code]]);
        }

        $names = DB::table('order_status')->pluck('name', 'id');
        $this->info("Created {$stats['orders']} orders ({$stats['lines']} lines) for " . count($customers) . ' fictional dealers.');
        $this->info('Revenue (incl. VAT): R ' . number_format($stats['revenue'] / 100, 2));
        $this->info("Stock transactions written: {$stats['stock_tx']} across " . count($touched) . ' products.');
        ksort($stats['status']);
        foreach ($stats['status'] as $id => $n) {
            $this->line(sprintf('  %-22s %d', $names[$id] ?? "status $id", $n));
        }
    }

    private function createCustomers(int $count, int $companyId, int $userId): array
    {
        $names = ['Karoo Trading Co', 'Amatola Supplies', 'Wild Coast Wholesalers', 'Kowie Traders',
            'Sunshine Coast Retail', 'Gonubie Distributors', 'Great Fish Supplies', 'Tsitsikamma Stores',
            'Winterberg Merchants', 'Hogsback Mini Market', 'Fish River Cash & Carry', 'Kei Valley Traders'];
        $towns = ['East London', 'Gqeberha', 'Komani', 'Makhanda', 'Mthatha', 'Gonubie', 'Stutterheim', 'Kei Mouth'];
        $category = CustomerCategory::value('AccountType');
        $next = (int) Customer::getNextCustomerNumber();
        $levels = [1, 1, 2, 2, 3, 1, 2, 3, 1, 2, 1, 3];

        $out = [];
        for ($i = 0; $i < $count; $i++) {
            $main = sprintf('%06d', $next + $i);
            $name = $names[$i % count($names)] . ($i >= count($names) ? ' ' . ($i + 1) : '');
            $town = $towns[$i % count($towns)];

            $out[] = Customer::create([
                'company_id' => $companyId,
                'acc_main' => $main,
                'acc_sub' => '000',
                'acc_code' => $main . '-000',
                'CustomerName' => $name,
                'CustomerCategoryID' => $category,
                'StoreEAN' => self::EAN_PREFIX . sprintf('%08d', $i + 1),
                'VatNr' => '4' . sprintf('%09d', $i + 1),
                'CreditLimit' => [50000, 100000, 150000, 250000][$i % 4],
                'AccountOpenedDate' => now()->subYear()->toDateString(),
                'StandardDiscountPercentage' => 0,
                'IsStatementSent' => 0,
                'IsOnCreditHold' => 0,
                'PaymentDays' => 30,
                'PhoneNumber' => '000 000 ' . sprintf('%04d', $i + 1),
                'GeneralEmailAddress' => 'demo' . ($i + 1) . '@example.com',
                'DeliveryAddressLine1' => ($i + 1) . ' Demo Street',
                'DeliveryCity' => $town,
                'DeliveryPostalCode' => '5200',
                'PostalAddressLine1' => 'PO Box ' . (100 + $i),
                'PostalCity' => $town,
                'PostalPostalCode' => '5200',
                'CustomerStatus' => 1,
                'LastEditedBy' => $userId,
                'price_level' => $levels[$i % count($levels)],
                'discount_allowed' => 1,
            ]);
        }

        return $out;
    }

    /** Back-dated timestamps, oldest first, trending upward, weekdays only, with a few today. */
    private function orderTimestamps(int $months, int $perMonth): array
    {
        $window = $months * 30;
        $today = Carbon::today();
        $stamps = [];

        for ($i = 0; $i < $months * $perMonth; $i++) {
            $t = sqrt(mt_rand() / mt_getrandmax());   // biased towards recent days
            $d = $today->copy()->subDays((int) round((1 - $t) * $window));
            if ($d->isSaturday()) $d->subDay();
            if ($d->isSunday()) $d->subDays(2);
            $ts = $d->setTime(rand(7, 16), rand(0, 59), rand(0, 59));
            $stamps[] = $ts->gt(now()) ? now()->subMinutes(rand(1, 60)) : $ts;
        }

        // Make sure "Today" on the dashboard is not empty.
        $minutesIntoDay = max(1, (int) $today->diffInMinutes(now()));
        for ($i = 0; $i < 3; $i++) {
            $stamps[] = now()->subMinutes(rand(1, $minutesIntoDay));
        }

        usort($stamps, fn ($a, $b) => $a <=> $b);
        return $stamps;
    }

    private function statusFor(Carbon $ts): int
    {
        $age = (int) $ts->diffInDays(now());
        $r = mt_rand(1, 100);

        if ($age >= 14) {                                   // old: almost all completed
            return $r <= 4 ? 9 : ($r <= 12 ? 4 : 8);
        }
        if ($age >= 3) {                                    // this fortnight: mixed pipeline
            return $r <= 3 ? 9 : ($r <= 40 ? 8 : ($r <= 60 ? 4 : ($r <= 75 ? 3 : ($r <= 88 ? 6 : 7))));
        }
        return $r <= 40 ? 1 : ($r <= 65 ? 2 : ($r <= 75 ? 5 : ($r <= 90 ? 7 : 6)));   // last 3 days
    }

    private function writeStatusHistory(int $orderId, int $final, bool $collection, Carbon $start, int $userId): void
    {
        $ready = $collection ? 6 : 7;
        $path = match ($final) {
            1 => [1],
            2 => [1, 2],
            5 => [1, 5],
            9 => [1, 9],
            6, 7 => [1, 2, $ready],
            3 => [1, 2, 7, 3],
            4 => $collection ? [1, 2, 6, 4] : [1, 2, 7, 3, 4],
            8 => $collection ? [1, 2, 6, 4, 8] : [1, 2, 7, 3, 4, 8],
        };

        $at = $start->copy();
        $old = null;
        foreach ($path as $i => $new) {
            if ($i > 0) $at = $at->copy()->addMinutes(rand(30, 600));
            $when = $at->gt(now()) ? now() : $at;
            DB::table('order_status_histories')->insert([
                'order_id' => $orderId,
                'old_status_id' => $old,
                'new_status_id' => $new,
                'changed_by_type' => $i === 0 ? 'system' : 'user',
                'changed_by_id' => $i === 0 ? null : $userId,
                'notes' => null,
                'changed_at' => $when,
                'created_at' => $when,
                'updated_at' => $when,
            ]);
            $old = $new;
        }
    }

    /** Weighted random index. */
    private function pick(array $weights): int
    {
        $r = mt_rand() / mt_getrandmax() * array_sum($weights);
        foreach ($weights as $i => $w) {
            if (($r -= $w) <= 0) return $i;
        }
        return array_key_last($weights);
    }

    private function purge(): void
    {
        // Put the stock back first.
        $txns = DB::table('stock_transactions')->where('notes', 'like', self::TAG . '%')->get();
        foreach ($txns as $t) {
            DB::table('stock_item_holdings')
                ->where('StockCode', $t->StockCode)->where('LocationCode', $t->LocationCode)
                ->increment('QuantityOnHand', -((float) $t->quantity_change));
        }
        DB::table('stock_transactions')->where('notes', 'like', self::TAG . '%')->delete();

        $ids = DB::table('orders')->where('InternalComments', self::TAG)->pluck('id');
        DB::table('order_status_histories')->whereIn('order_id', $ids)->delete();
        DB::table('orders_items')->whereIn('OrderID', $ids)->delete();
        DB::table('orders')->whereIn('id', $ids)->delete();

        $customers = DB::table('customers')->where('StoreEAN', 'like', self::EAN_PREFIX . '%')->delete();

        $this->info("Removed {$ids->count()} orders, {$txns->count()} stock transactions, {$customers} demo customers. Stock restored.");
    }
}
