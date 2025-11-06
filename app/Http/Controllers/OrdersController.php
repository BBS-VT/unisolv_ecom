<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Order;
use App\Services\LocationAssignmentService;
use Illuminate\Http\Request;
use App\Http\Requests\Order\Store;
use App\Http\Requests\Order\Update;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
use App\Models\Customer;
use DB;
use Gate;
use PDF;
use Symfony\Component\HttpFoundation\Response;
use Mpociot\VatCalculator\Facades\VatCalculator;
use Carbon\Carbon;
use Spatie\ArrayToXml\ArrayToXml;
use Session;
use App\Mail\FulfillmentNotificationMail;
use App\Mail\OrderConfirmationMail;
use App\Notifications\NewOrderNotification;
use Illuminate\Support\Facades\Notification;

class OrdersController extends Controller
{
    /**
     * Display Orders Page
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        abort_if(Gate::denies('order_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $user = $request->user();
        $currentCompany = $user->currentCompany();

        // Query Invoices by Company and Tab
        if ($request->tab == 'all') {
            $query = Order::findByCompany($currentCompany->id)->orderBy('created_at', 'desc');
            $tab = 'all';
        } else {
            if ($request->tab == 'processed') {
                $query = Order::findByCompany($currentCompany->id)->active()->orderBy('created_at', 'desc');
                $tab = 'processed';
            } elseif ($request->tab == 'onhold') {
                $query = Order::findByCompany($currentCompany->id)->onHold()->orderBy('created_at', 'desc');
                $tab = 'onhold';
            } else {
                $query = Order::findByCompany($currentCompany->id)->new()->orderBy('OrderNumber', 'desc');
                $tab = 'new';
            }
        }

        // Apply Filters and Paginate
        $orders = QueryBuilder::for($query)
            ->allowedFilters([
                AllowedFilter::partial('OrderNumber'),
                AllowedFilter::scope('from'),
                AllowedFilter::scope('to'),
            ])
            ->paginate()
            ->appends(request()->query());

        //echo "<pre>"; print_r($orders); die;
        return view('orders.index', compact('orders', 'tab'));

    }

    /**
     * Display form to create new Order
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        abort_if(Gate::denies('order_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $user = $request->user();
        $currentCompany = $user->currentCompany();

        // Get next Order number if auto generation option is enabled
        $next_order_number = Order::getNextOrderNumber();

        // Get customers based on Sales Rep
        $salesrep = auth()->user();

        if ($salesrep->IsSalesperson == '1') {
            $customers = DB::table('customers')->where('SalesRepID', auth()->user()->RepCode)->pluck('CustomerName',
                'acc_main')->prepend(trans('global.pleaseSelect'), '');
        } else {
            $customers = Customer::all()->pluck('CustomerName', 'acc_main')->prepend(trans('global.pleaseSelect'), '');
        }

        // Create new number model and set order_number and company_id
        // so that we can use it in the form
        $order = new Order();
        $order->order_number = $next_order_number;
        $order->company_id = $currentCompany->id;

        $products = Product::all();
        $tax_per_item = (boolean) $currentCompany->getSetting('tax_per_item');
        $discount_per_item = (boolean) $currentCompany->getSetting('discount_per_item');
        $display_selling_prices = (boolean) $currentCompany->getSetting('display_selling_prices');
        $display_cost_prices = (boolean) $currentCompany->getSetting('display_cost_prices');

        return view('orders.create',
            compact('order', 'customers', 'products', 'tax_per_item', 'discount_per_item', 'currentCompany',
                'display_selling_prices', 'display_cost_prices'));
    }

    /**
     * Store the Order in the Database
     *
     * @param  \App\Http\Requests\Order\Store  $request
     *
     * @return \Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
     */
    public function store(Store $request)
    {

        $user = $request->user();
        $currentCompany = $user->currentCompany();

        // Get company based settings
        $tax_per_item = (boolean) $currentCompany->getSetting('tax_per_item');
        $discount_per_item = (boolean) $currentCompany->getSetting('discount_per_item');

        // Save Order to Database
        $order = Order::create([
            'OrderDate' => $request->order_date,
            'OrderNumber' => $request->order_number,
            'CustomerPurchaseOrderNumber' => $request->reference_number,
            'CustomerID' => $request->customer_id,
            'company_id' => $currentCompany->id,
            'SalesPersonID' => $request->salesperson_id,
            'LastEditedBy' => $request->salesperson_id,
            'OrderStatusID' => '1',
            'Authorisation' => '0',
            'sub_total' => $request->sub_total,
            'discount_type' => 'percent',
            'discount_val' => $request->total_discount ?? 0,
            'total' => $request->grand_total,
            'Comments' => $request->notes,
            'InternalComments' => $request->private_notes,
            'tax_per_item' => $tax_per_item,
            'discount_per_item' => $discount_per_item,
        ]);

        // Arrays of data for Order Items
        $products = $request->product;
        $quantities = $request->quantity;
        $taxes = $request->taxes;
        $prices = $request->price;
        $totals = $request->total;
        $discounts = $request->discount;

        // Add products (order items)
        for ($i = 0; $i < count($request->product); $i++) {
            if (isset($request->quantity[$i]) && isset($request->price[$i])) {

                $stockItem = Product::find($request->product[$i]);

                $item = $order->items()->create([
                    'OrderID' => $request->order_number,
                    'company_id' => $currentCompany->id,
                    'StockItem' => $stockItem->StockCode,
                    'LocationCode' => LocationAssignmentService::assignLocation(
                        $stockItem->StockCode,
                        $request->quantity[$i],
                        null
                    ),
                    'discount_type' => 'percent',
                    'discount_val' => $request->discount[$i] ?? 0,
                    'Quantity' => $request->quantity[$i],
                    'UnitPrice' => $request->price[$i],
                    'total' => $request->total[$i],
                    'LastEditedBy' => $request->salesperson_id,
                    'ContractDiscount' => '0',
                ]);

                if ($taxes && array_key_exists($i, $taxes)) {
                    foreach ($taxes[$i] as $tax) {
                        $item->taxes()->create([
                            'tax_type_id' => $tax
                        ]);
                    }
                }


            }
        }

        // If Order based taxes are given
        if ($request->has('total_taxes')) {
            foreach ($request->total_taxes as $tax) {
                $order->taxes() - create([
                    'tax_type_id' => $tax
                ]);
            }
        }

        // Fetch notification settings
        $orderCustomerConfirmation = (boolean) $currentCompany->getSetting('order_customer_confirmation');
        $orderFulfillmentNotification = (boolean) $currentCompany->getSetting('order_fulfillment_notification');
        $fulfillmentEmail = $currentCompany->getSetting('fulfillment_mailbox');

        // Send confirmation email to customer
        if ($orderCustomerConfirmation && $order->customer->GeneralEmailAddress) {
            Mail::to($order->customer->GeneralEmailAddress)->send(new OrderConfirmationMail($order));
        }

        // Notify fulfillment team
        if ($orderFulfillmentNotification && $fulfillmentEmail) {
            Mail::to($fulfillmentEmail)->send(new FulfillmentNotificationMail($order));
        } else {
            \Log::warning('Fulfillment mailbox not configured');
        }
        $fulfillmentTeam = User::whereHas('roles', function ($query) {
            $query->where('title', 'Fulfillment');
        })->get();
        Notification::send($fulfillmentTeam, new NewOrderNotification($order));

        session()->flash('alert-success', __('global.order_added'));
        return redirect()->route('orders.index');
    }

    public function show(Order $order)
    {
        abort_if(Gate::denies('order_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $order->load('items');
        $order->load('customer');

        //dd($order);

        return view('orders.details', compact('order'));
    }

    /**
     * Delete an Order
     *
     * @param  Request  $request
     * @return \Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
     */
    public function delete(Request $request)
    {
        abort_if(Gate::denies('order_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $order = Order::findOrFail($request->order);

        // Delete order form Database
        $order->delete();

        session()->flash('alert-success', __('global.order_deleted'));
        return redirect()->route('orders.index');
    }

    /**
     * Download order to Unisolv
     *
     * @param  Order  $order
     */

    public function downloadOrder(Order $order)
    {

        Order::where('id', $order->id)
            ->update([
                'LastEditedBy' => auth()->user()->id, 'OrderStatusID' => '2',
                'updated_at' => Carbon::now()->toDateTimeString()
            ]);

        $document = new \DOMDocument("1.0", "UTF-8");
        $document->appendChild($document->createElement('OrderMessage'));

        $rootTag = $document->documentElement;
        $headerTag = $rootTag->appendChild(
            $document->createElement("StandardBusinessDocumentHeader")
        );
        $senderTag = $headerTag->appendChild(
            $document->createElement("Sender")
        );
        /*$senderTag
            ->appendChild($document->createElement("Identifier"))
            ->appendChild($document->createTextNode($order->customer->StoreEAN));*/
        $senderTag
            ->appendChild($document->createElement("Identifier"))
            ->appendChild($document->createTextNode($order->customer->acc_main.$order->customer->acc_sub));

        $receiverTag = $headerTag->appendChild(
            $document->createElement("Receiver")
        );
        $receiverTag
            ->appendChild($document->createElement("Identifier"))
            ->appendChild($document->createTextNode("0000000000000"));

        $identificationTag = $headerTag->appendChild(
            $document->createElement("DocumentIdentification")
        );
        $identificationTag
            ->appendChild($document->createElement("Standard"))
            ->appendChild($document->createTextNode("GS1"));
        $identificationTag
            ->appendChild($document->createElement("TypeVersion"))
            ->appendChild($document->createTextNode("3.2"));
        $identificationTag
            ->appendChild($document->createElement("InstanceIdentifier"))
            ->appendChild($document->createTextNode($order->CustomerPurchaseOrderNumber));
        $identificationTag
            ->appendChild($document->createElement("Type"))
            ->appendChild($document->createTextNode("Order"));
        $identificationTag
            ->appendChild($document->createElement("MultipleType"))
            ->appendChild($document->createTextNode("true"));
        $identificationTag
            ->appendChild($document->createElement("CreationDateAndTime"))
            ->appendChild($document->createTextNode(Carbon::now()->toDateTimeString()));

        $manifestTag = $headerTag->appendChild(
            $document->createElement("Manifest")
        );
        $manifestTag
            ->appendChild($document->createElement("NumberOfItems"))
            ->appendChild($document->createTextNode("1"));

        $orderTag = $rootTag->appendChild(
            $document->createElement("order")
        );
        $orderTag->appendChild($document->createAttribute("xmlns"));
        $orderTag
            ->appendChild($document->createElement("creationDateTime"))
            ->appendChild($document->createTextNode($order->created_at));
        $orderTag
            ->appendChild($document->createElement("documentStatusCode"))
            ->appendChild($document->createTextNode("ORIGINAL"));
        $orderTag
            ->appendChild($document->createElement("documentActionCode"))
            ->appendChild($document->createTextNode("ADD"));

        $orderidentityTag = $orderTag->appendChild(
            $document->createElement("orderIdentification")
        );
        $orderidentityTag
            ->appendChild($document->createElement("entityIdentification"))
            ->appendChild($document->createTextNode($order->OrderNumber));
        $orderTag
            ->appendChild($document->createElement("orderTypeCode"))
            ->appendChild($document->createTextNode("220"));

        $orderTag
            ->appendChild($document->createElement("additionalOrderInstruction"))
            ->appendChild($document->createTextNode(($order->CustomerPurchaseOrderNumber)));
        $orderbuyerTag = $orderTag->appendChild(
            $document->createElement("buyer")
        );
        /*$orderbuyerTag
            ->appendChild($document->createElement("gln"))
            ->appendChild($document->createTextNode($order->customer->StoreEAN));*/
        $orderbuyerTag
            ->appendChild($document->createElement("gln"))
            /*->appendChild($document->createTextNode($order->customer->acc_main.$order->customer->acc_sub));*/
            ->appendChild($document->createTextNode($order->customer->acc_main));
        $orderbuyerTag
            ->appendChild($document->createElement("additionalPartyIdentification"))
            ->appendChild($document->createTextNode($order->customer->acc_main));
        $orderbuyerTag
            ->appendChild($document->createElement("additionalPartyIdentification"))
            ->appendChild($document->createTextNode($order->customer->CustomerName));
        $ordersellerTag = $orderTag->appendChild(
            $document->createElement("seller")
        );
        $ordersellerTag
            ->appendChild($document->createElement("gln"))
            ->appendChild($document->createTextNode("6004700000054"));
        $ordersellerTag
            ->appendChild($document->createElement("additionalPartyIdentification"))
            ->appendChild($document->createTextNode("400197"));
        $ordersellerTag
            ->appendChild($document->createElement("additionalPartyIdentification"))
            ->appendChild($document->createTextNode("Quenera Distribution"));
        foreach ($order->items as $key) {
            $orderlineitemTag = $orderTag->appendChild(
                $document->createElement("orderLineItem")
            );
            $orderlineitemTag
                ->appendChild($document->createElement("lineItemNumber"))
                ->appendChild($document->createTextNode($key->id));
            $orderlineitemTag
                ->appendChild($document->createElement("requestedQuantity"))
                ->appendChild($document->createTextNode($key->Quantity));
            $orderlineitemTag
                ->appendChild($document->createElement("additionalOrderLineInstruction"))
                ->appendChild($document->createTextNode(" "));

            $orderlineitemTag
                ->appendChild($document->createElement("netAmount"))
                ->appendChild($document->createTextNode(number_format(($key->product->SellingPrice / 1.15), 2, ".",
                    " ")));

            $orderlineitemTag
                ->appendChild($document->createElement("netPrice"))
                ->appendChild($document->createTextNode(number_format($key->product->SellingPrice, 2, ".", " ")));

            $orderlineitemTag
                ->appendChild($document->createElement("discountPercentage"))
                ->appendChild($document->createTextNode(bcdiv($key->discount_val, 1, 2)));
            $orderlineitemTag
                ->appendChild($document->createElement("monetaryAmountExcludingTaxes"))
                ->appendChild($document->createTextNode(number_format((($key->product->SellingPrice * $key->Quantity) / 1.15),
                    2, ".", " ")));
            $orderlineitemTag
                ->appendChild($document->createElement("monetaryAmountIncludingTaxes"))
                ->appendChild($document->createTextNode(number_format(($key->product->SellingPrice * $key->Quantity), 2,
                    ".", " ")));

            $ordertradeitemTag = $orderlineitemTag->appendChild(
                $document->createElement("transactionalTradeItem")
            );

            $ordertradeitemTag
                ->appendChild($document->createElement("gtin"))
                ->appendChild($document->createTextNode(empty($key->product->Barcode) ? $key->product->StockCode : $key->product->Barcode));
            $ordertradeitemTag
                ->appendChild($document->createElement("additionalTradeItemIdentification"))
                ->appendChild($document->createTextNode(""));
            $ordertradeitemTag
                ->appendChild($document->createElement("additionalTradeItemIdentification"))
                ->appendChild($document->createTextNode($key->product->StockCode));
            $ordertradeitemTag
                ->appendChild($document->createElement("additionalTradeItemIdentification"))
                ->appendChild($document->createTextNode($key->product->StockItemName));
            $ordertradeitemTag
                ->appendChild($document->createElement("tradeItemDescription"))
                ->appendChild($document->createTextNode($key->product->StockItemName));
            $ordertradeitemTag
                ->appendChild($document->createElement("stockLocation"))
                ->appendChild($document->createTextNode($key->LocationCode));

            $ordertradeitemColorTag = $ordertradeitemTag->appendChild(
                $document->createElement("color")
            );
            $ordertradeitemColorTag
                ->appendChild($document->createElement("colorDescription"))
                ->appendChild($document->createTextNode($key->product->Packsize));

            $ordertradeitemSizeTag = $ordertradeitemTag->appendChild(
                $document->createElement("size")
            );
            $ordertradeitemSizeTag
                ->appendChild($document->createElement("descriptiveSize"))
                ->appendChild($document->createTextNode($key->product->Size));

            $ordertradeitemPromotionTag = $orderlineitemTag->appendChild(
                $document->createElement("promotionalDeal")
            );
            $ordertradeitemPromotionTag
                ->appendChild($document->createElement("entityIdentification"))
                ->appendChild($document->createTextNode("0000000000"));

            $orderlineitemDetailTag = $orderlineitemTag->appendChild(
                $document->createElement("orderLineItemDetail")
            );
            $orderlineitemDetailTag
                ->appendChild($document->createElement("requestedQuantity"))
                ->appendChild($document->createTextNode($key->Quantity));

            $orderlogisicalinfoTag = $orderlineitemDetailTag->appendChild(
                $document->createElement("orderLogisticalInformation")
            );
            $orderlogisicalshipTag = $orderlogisicalinfoTag->appendChild(
                $document->createElement("shipTo")
            );

            $orderlogisicalshipTag
                ->appendChild($document->createElement("gln"))
                ->appendChild($document->createTextNode($order->customer->acc_main.$order->customer->acc_sub));
            $orderlogisicalshipTag
                ->appendChild($document->createElement("additionalPartyIdentification"))
                ->appendChild($document->createTextNode($order->customer->acc_main));
            $orderlogisicalshipTag
                ->appendChild($document->createElement("additionalPartyIdentification"))
                ->appendChild($document->createTextNode($order->customer->CustomerName));
            $orderlogisicaldateTag = $orderlogisicalinfoTag->appendChild(
                $document->createElement('orderLogisticalDateInformation')
            );
            $orderlogisicalreqdateTag = $orderlogisicaldateTag->appendChild(
                $document->createElement('requestedDeliveryDateTime')
            );
            $orderlogisicalreqdateTag
                ->appendChild($document->createElement('date'))
                ->appendChild($document->createTextNode(Carbon::now()->toDateTimeString()));

            $avpListTag = $orderlineitemDetailTag->appendChild(
                $document->createElement('avpList')
            );
            $avpListTag
                ->appendChild($document->createElement('eComStringAttributeValuePairList'))
                ->appendChild($document->createTextNode($order->customer->StandardDiscountPercentage));

            $avpListMainTag = $orderlineitemTag->appendChild(
                $document->createElement('avpList')
            );
            $avpListMainTag
                ->appendChild($document->createElement('eComStringAttributeValuePairList'))
                ->appendChild($document->createTextNode($key->product->PackSize));
            $avpListMainTag
                ->appendChild($document->createElement('eComStringAttributeValuePairList'))
                ->appendChild($document->createTextNode('EA'));
        }

        $document->formatOutput = true;
        header('Content-type: "application/force-download"; charset="utf8"');
        header('Content-disposition: attachment; filename="Order'.$order->OrderNumber.'.SCO"');
        echo $document->saveXML();

    }
}
