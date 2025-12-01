<?php

namespace App\Livewire\SalesOrder;

use Livewire\Component;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\TaxType;
use App\Models\StockItemHoldings;
use App\Services\PricingService;
use App\Services\DiscountService;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class OrderForm extends Component
{
    // Order header properties
    public $orderId; // For editing existing orders
    public $orderNumber;
    public $customerId;
    public $customerName;
    public $referenceNumber;
    public $orderDate;
    public $notes;
    public $privateNotes;
    public $salespersonId;

    // Customer context
    public $customerCurrency;
    public $customerPriceLevel = 1;
    public $customerDiscountAllowed = true;
    public $customerStandardDiscount = 0;

    // Order lines - array of line items
    public $orderLines = [];

    // Totals
    public $subTotal = 0;
    public $grandTotal = 0;
    public $taxBreakdown = [];
    public $totalDiscount = 0;

    // Settings
    public $taxPerItem = true;
    public $discountPerItem = true;

    // Services (injected)
    protected $pricingService;
    protected $discountService;

    // Validation messages
    protected $messages = [
        'customerId.required' => 'Please select a customer',
        'referenceNumber.required' => 'Reference number is required',
        'orderDate.required' => 'Order date is required',
        'orderLines.*.product_id.required' => 'Please select a product',
        'orderLines.*.quantity.required' => 'Quantity is required',
        'orderLines.*.quantity.min' => 'Quantity must be at least 0.01',
        'orderLines.*.price.required' => 'Price is required',
        'orderLines.*.price.min' => 'Price must be at least 0',
    ];

    public function boot(PricingService $pricingService, DiscountService $discountService)
    {
        $this->pricingService = $pricingService;
        $this->discountService = $discountService;
    }

    public function mount($orderId = null)
    {
        $user = auth()->user();
        $currentCompany = $user->currentCompany();

        // Get settings
        $this->taxPerItem = (bool) $currentCompany->getSetting('tax_per_item');
        $this->discountPerItem = (bool) $currentCompany->getSetting('discount_per_item');

        // Set salesperson
        $this->salespersonId = $user->RepCode ?? $user->id;

        // Set order date
        $this->orderDate = Carbon::today()->format('Y-m-d');

        if ($orderId) {
            // Load existing order
            $this->loadOrder($orderId);
        } else {
            // New order
            $this->orderNumber = Order::getNextOrderNumber();
            $this->addLine(); // Start with one empty line
        }
    }

    /**
     * When customer changes, update customer context
     */
    public function updatedCustomerId($value)
    {
        if (!$value) {
            $this->resetCustomerContext();
            return;
        }

        $customer = Customer::find($value);

        if (!$customer) {
            $this->resetCustomerContext();
            return;
        }

        // Update customer context
        $this->customerName = $customer->CustomerName;
        $this->customerCurrency = $customer->currency_id;
        $this->customerPriceLevel = $customer->price_level ?? 1;
        $this->customerDiscountAllowed = $customer->discount_allowed ?? true;
        $this->customerStandardDiscount = $customer->StandardDiscountPercentage ?? 0;

        // Recalculate all existing lines with new customer context
        $this->recalculateAllLines();
    }

    /**
     * Reset customer context when customer is deselected
     */
    private function resetCustomerContext()
    {
        $this->customerName = null;
        $this->customerCurrency = null;
        $this->customerPriceLevel = 1;
        $this->customerDiscountAllowed = true;
        $this->customerStandardDiscount = 0;

        // Clear all order lines
        $this->orderLines = [];
        $this->addLine();
        $this->calculateTotals();
    }

    /**
     * Add a new line to the order
     */
    public function addLine()
    {
        $this->orderLines[] = [
            'id' => uniqid(), // Temporary ID for Livewire tracking
            'product_id' => null,
            'product_code' => '',
            'product_name' => '',
            'quantity' => 1,
            'price' => 0,
            'price2' => 0,
            'price3' => 0,
            'price4' => 0,
            'avg_cost' => 0,
            'last_cost' => 0,
            'discount_percent' => 0,
            'max_discount' => 100,
            'discount_locked' => false,
            'discount_reason' => '',
            'line_total' => 0,
            'taxes' => [],
            'stock_on_hand' => 0,
            'is_contract_price' => false,
            'is_contract_discount' => false,
        ];
    }

    /**
     * Remove a line from the order
     */
    public function removeLine($index)
    {
        unset($this->orderLines[$index]);
        $this->orderLines = array_values($this->orderLines); // Re-index array
        $this->calculateTotals();
    }

    /**
     * When a product is selected on a line
     */
    public function productSelected($lineIndex, $productId)
    {
        if (!$this->customerId) {
            $this->addError('customerId', 'Please select a customer first');
            // Reset the product selection
            $this->orderLines[$lineIndex]['product_id'] = null;
            return;
        }

        if (!$productId) {
            return;
        }

        $product = Product::with(['stockHolding', 'defaultTax'])->find($productId);

        if (!$product) {
            return;
        }

        // Get pricing for this product and customer
        $pricing = $this->pricingService->getProductPricing($product, $this->customerId);

        // Get discount rules for this product and customer
        $discountRules = $this->discountService->getApplicableDiscount(
            $product,
            $this->customerId,
            $pricing['is_contract']
        );

        // Get stock on hand
        $stockOnHand = StockItemHoldings::getTotalQuantityForProduct($product->StockCode);

        // Set taxes based on product's TaxIndicator
        $taxes = [];
        if ($pricing['default_tax_id']) {
            $taxes = [$pricing['default_tax_id']];
        }

        // Update the line with product data
        $this->orderLines[$lineIndex]['product_id'] = $product->id;
        $this->orderLines[$lineIndex]['product_code'] = $product->StockCode;
        $this->orderLines[$lineIndex]['product_name'] = $product->StockItemName;
        $this->orderLines[$lineIndex]['price'] = $pricing['price'];
        $this->orderLines[$lineIndex]['price2'] = $pricing['price2'];
        $this->orderLines[$lineIndex]['price3'] = $pricing['price3'];
        $this->orderLines[$lineIndex]['price4'] = $pricing['price4'];
        $this->orderLines[$lineIndex]['avg_cost'] = $pricing['avg_cost'];
        $this->orderLines[$lineIndex]['last_cost'] = $pricing['last_cost'];
        $this->orderLines[$lineIndex]['stock_on_hand'] = $stockOnHand;
        $this->orderLines[$lineIndex]['is_contract_price'] = $pricing['is_contract'];

        // Set discount based on rules
        $this->orderLines[$lineIndex]['discount_percent'] = $discountRules['discount'];
        $this->orderLines[$lineIndex]['max_discount'] = $discountRules['max_discount'];
        $this->orderLines[$lineIndex]['discount_locked'] = $discountRules['is_locked'];
        $this->orderLines[$lineIndex]['discount_reason'] = $discountRules['reason'];
        $this->orderLines[$lineIndex]['is_contract_discount'] = $discountRules['is_contract'];

        // Set taxes automatically
        $this->orderLines[$lineIndex]['taxes'] = $taxes;
        $this->orderLines[$lineIndex]['tax_rate'] = $pricing['tax_rate'];

        // Calculate line total
        $this->calculateLineTotal($lineIndex);
    }

    /**
     * When quantity, price, or discount changes on a line
     */
    public function updatedOrderLines($value, $key)
    {
        // $key will be like "0.quantity" or "1.discount_percent"
        $parts = explode('.', $key);
        $lineIndex = (int) $parts[0];
        $field = $parts[1] ?? null;

        // Validate the line still has a product
        if (!isset($this->orderLines[$lineIndex]['product_id']) || !$this->orderLines[$lineIndex]['product_id']) {
            return;
        }

        // If discount changed, validate it
        if ($field === 'discount_percent') {
            $this->validateLineDiscount($lineIndex);
        }

        // If price changed manually, mark as non-contract price
        if ($field === 'price') {
            $this->orderLines[$lineIndex]['is_contract_price'] = false;
        }

        // Recalculate line total
        $this->calculateLineTotal($lineIndex);
    }

    /**
     * Validate discount on a line
     */
    private function validateLineDiscount($lineIndex)
    {
        $line = $this->orderLines[$lineIndex];

        // If discount is locked, reset to the locked value
        if ($line['discount_locked']) {
            $product = Product::find($line['product_id']);
            $discountRules = $this->discountService->getApplicableDiscount(
                $product,
                $this->customerId,
                $line['is_contract_price']
            );

            $this->orderLines[$lineIndex]['discount_percent'] = $discountRules['discount'];

            $this->addError(
                "orderLines.{$lineIndex}.discount_percent",
                $discountRules['reason']
            );

            return;
        }

        // Validate against max discount
        $requestedDiscount = (float) $line['discount_percent'];
        $maxDiscount = (float) $line['max_discount'];

        if ($requestedDiscount > $maxDiscount) {
            $this->addError(
                "orderLines.{$lineIndex}.discount_percent",
                "Discount cannot exceed {$maxDiscount}%"
            );

            // Reset to max allowed
            $this->orderLines[$lineIndex]['discount_percent'] = $maxDiscount;
        }
    }

    /**
     * Calculate line total for a specific line
     */
    private function calculateLineTotal($lineIndex)
    {
        $line = $this->orderLines[$lineIndex];

        if (!$line['product_id']) {
            $this->orderLines[$lineIndex]['line_total'] = 0;
            $this->calculateTotals();
            return;
        }

        // Get values
        $quantity = (float) $line['quantity'];
        $price = (float) $line['price'];
        $discount = (float) $line['discount_percent'];

        // Calculate base amount
        $amount = $quantity * $price;

        // Apply discount
        if ($discount > 0) {
            $discountAmount = ($amount * $discount) / 100;
            $amount -= $discountAmount;
        }

        // If tax per item is enabled, we could add taxes here
        // For now, keeping it simple - taxes calculated at total level

        // Store line total
        $this->orderLines[$lineIndex]['line_total'] = round($amount, 2);

        // Recalculate order totals
        $this->calculateTotals();
    }

    /**
     * Calculate order totals
     */
    private function calculateTotals()
    {
        $this->subTotal = 0;
        $this->taxBreakdown = [];

        // Sum all line totals
        foreach ($this->orderLines as $line) {
            if (!$line['product_id']) {
                continue;
            }

            $lineTotal = (float) $line['line_total'];
            $this->subTotal += $lineTotal;

            // Calculate taxes for this line if tax_per_item is enabled
            if ($this->taxPerItem) {
                foreach ($line['taxes'] as $taxId) {
                    $tax = TaxType::find($taxId);
                    if (!$tax) {
                        continue;
                    }

                    $taxAmount = ($lineTotal * $tax->TaxRate) / 100;

                    $taxKey = $tax->TaxTypeName . ' (' . $tax->TaxRate . '%)';

                    if (!isset($this->taxBreakdown[$taxKey])) {
                        $this->taxBreakdown[$taxKey] = 0;
                    }
                    $this->taxBreakdown[$taxKey] += $taxAmount;
                }
            }
        }

        // Calculate grand total
        $this->grandTotal = $this->subTotal;

        // Add all taxes
        foreach ($this->taxBreakdown as $taxAmount) {
            $this->grandTotal += $taxAmount;
        }

        // Apply total discount if not discount_per_item
        if (!$this->discountPerItem && $this->totalDiscount > 0) {
            $totalDiscountAmount = ($this->subTotal * $this->totalDiscount) / 100;
            $this->grandTotal -= $totalDiscountAmount;
        }

        // Round
        $this->subTotal = round($this->subTotal, 2);
        $this->grandTotal = round($this->grandTotal, 2);
    }

    /**
     * Recalculate all lines (when customer changes)
     */
    private function recalculateAllLines()
    {
        foreach ($this->orderLines as $index => $line) {
            if ($line['product_id']) {
                $this->productSelected($index, $line['product_id']);
            }
        }
    }

    /**
     * Save the order
     */
    public function save()
    {
        // Validate
        $this->validate([
            'customerId' => 'required|exists:customers,acc_main',
            'referenceNumber' => 'required|string|max:255',
            'orderDate' => 'required|date',
            'orderLines' => 'required|array|min:1',
            'orderLines.*.product_id' => 'required|exists:products,id',
            'orderLines.*.quantity' => 'required|numeric|min:0.01',
            'orderLines.*.price' => 'required|numeric|min:0',
        ]);

        // Check for validation errors (discount violations, etc.)
        if ($this->getErrorBag()->isNotEmpty()) {
            session()->flash('error', 'Please fix validation errors before saving');
            return;
        }

        try {
            DB::beginTransaction();

            $user = auth()->user();
            $currentCompany = $user->currentCompany();

            // Create or update order
            $orderData = [
                'OrderDate' => $this->orderDate,
                'OrderNumber' => $this->orderNumber,
                'CustomerPurchaseOrderNumber' => $this->referenceNumber,
                'CustomerID' => $this->customerId,
                'company_id' => $currentCompany->id,
                'SalesPersonID' => $this->salespersonId,
                'LastEditedBy' => $this->salespersonId,
                'OrderStatusID' => '1', // New order
                'Authorisation' => '0',
                'sub_total' => $this->subTotal,
                'discount_type' => 'percent',
                'discount_val' => $this->totalDiscount ?? 0,
                'total' => $this->grandTotal,
                'Comments' => $this->notes,
                'InternalComments' => $this->privateNotes,
                'tax_per_item' => $this->taxPerItem,
                'discount_per_item' => $this->discountPerItem,
            ];

            if ($this->orderId) {
                // Update existing order
                $order = Order::findOrFail($this->orderId);
                $order->update($orderData);

                // Delete existing items
                $order->items()->delete();
            } else {
                // Create new order
                $order = Order::create($orderData);
            }

            // Create order lines
            foreach ($this->orderLines as $line) {
                if (!$line['product_id']) {
                    continue;
                }

                $product = Product::find($line['product_id']);

                $item = $order->items()->create([
                    'OrderID' => $order->id,
                    'company_id' => $currentCompany->id,
                    'StockItem' => $line['product_id'],
                    'discount_type' => 'percent',
                    'discount_val' => $line['discount_percent'] ?? 0,
                    'Quantity' => $line['quantity'],
                    'UnitPrice' => $line['price'],
                    'total' => $line['line_total'],
                    'LastEditedBy' => $this->salespersonId,
                    'ContractDiscount' => $line['is_contract_discount'] ? '1' : '0',
                ]);

                // Attach taxes if tax_per_item is enabled
                if ($this->taxPerItem && !empty($line['taxes'])) {
                    foreach ($line['taxes'] as $taxId) {
                        $item->taxes()->create([
                            'tax_type_id' => $taxId,
                        ]);
                    }
                }
            }

            DB::commit();

            session()->flash('success', 'Sales order saved successfully');

            return redirect()->route('orders.show', $order->id);

        } catch (\Exception $e) {
            DB::rollBack();

            session()->flash('error', 'Error saving sales order: ' . $e->getMessage());
            \Log::error('Sales order save failed: ' . $e->getMessage());
        }
    }

    /**
     * Load an existing order for editing
     */
    private function loadOrder($orderId)
    {
        $order = Order::with(['items.product.taxes', 'items.product.stockHolding', 'items.taxes'])
            ->findOrFail($orderId);

        $this->orderId = $order->id;
        $this->orderNumber = $order->OrderNumber;
        $this->customerId = $order->CustomerID;
        $this->referenceNumber = $order->CustomerPurchaseOrderNumber;
        $this->orderDate = $order->OrderDate->format('Y-m-d');
        $this->notes = $order->Comments;
        $this->privateNotes = $order->InternalComments;
        $this->salespersonId = $order->SalesPersonID;
        $this->totalDiscount = $order->discount_val;

        // Set customer context
        $customer = Customer::find($order->CustomerID);
        if ($customer) {
            $this->customerName = $customer->CustomerName;
            $this->customerCurrency = $customer->currency_id;
            $this->customerPriceLevel = $customer->price_level ?? 1;
            $this->customerDiscountAllowed = $customer->discount_allowed ?? true;
            $this->customerStandardDiscount = $customer->StandardDiscountPercentage ?? 0;
        }

        // Load order lines
        $this->orderLines = [];
        foreach ($order->items as $item) {
            $product = $item->product;

            // Get current pricing and discount rules
            $pricing = $this->pricingService->getProductPricing($product, $this->customerId);
            $discountRules = $this->discountService->getApplicableDiscount(
                $product,
                $this->customerId,
                $item->ContractDiscount == '1'
            );

            $stockOnHand = StockItemHoldings::getTotalQuantityForProduct($product->StockCode);

            $this->orderLines[] = [
                'id' => $item->id,
                'product_id' => $item->StockItem,
                'product_code' => $product->StockCode,
                'product_name' => $product->StockItemName,
                'quantity' => $item->Quantity,
                'price' => $item->UnitPrice,
                'price2' => $pricing['price2'],
                'price3' => $pricing['price3'],
                'price4' => $pricing['price4'],
                'avg_cost' => $pricing['avg_cost'],
                'last_cost' => $pricing['last_cost'],
                'discount_percent' => $item->discount_val,
                'max_discount' => $discountRules['max_discount'],
                'discount_locked' => $discountRules['is_locked'],
                'discount_reason' => $discountRules['reason'],
                'line_total' => $item->total,
                'taxes' => $item->taxes->pluck('tax_type_id')->toArray(),
                'stock_on_hand' => $stockOnHand,
                'is_contract_price' => $item->ContractDiscount == '1',
                'is_contract_discount' => $item->ContractDiscount == '1',
            ];
        }

        $this->calculateTotals();
    }

    /**
     * Render the component
     */
    public function render()
    {
        $user = auth()->user();
        $currentCompany = $user->currentCompany();
        $salesrep = auth()->user();

        // Get all tax types
        $taxTypes = TaxType::all();

        return view('livewire.sales-order.order-form', [
            'taxTypes' => $taxTypes,
            'currentCompany' => $currentCompany,
        ])->layout('layouts.master', ['page' => 'orders']);
    }
}
