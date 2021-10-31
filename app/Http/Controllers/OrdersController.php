<?php


namespace App\Http\Controllers;

use App\Http\Requests\MassDestroyOrderRequest;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Models\OrdersItem;
use App\Models\Product;
use App\Models\Customer;
use App\Models\SpecialDeals;
use DB;
use Gate;
use PDF;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Mpociot\VatCalculator\Facades\VatCalculator;
use Carbon\Carbon;
use Spatie\ArrayToXml\ArrayToXml;
use Session;

class OrdersController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('order_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $orders = Order::with('customer')
            ->orderBy('OrderDate', 'desc')
            ->get();

        //echo "<pre>"; print_r($orders); die;
        return view('orders.index', compact('orders'));
    }

    public function create()
    {
        abort_if(Gate::denies('order_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $products = Product::all();
        $salesrep = auth()->user();

        if ($salesrep->IsSalesperson == '1'){
           $customers = DB::table('customers')->where('SalesRepID', auth()->user()->RepCode)->pluck('CustomerName', 'acc_main')->prepend(trans('global.pleaseSelect'), '');
        } else {
            $customers = Customer::all()->pluck('CustomerName', 'acc_main')->prepend(trans('global.pleaseSelect'), '');
        }

        $tax = VatCalculator::getTaxRateForLocation( 'ZA' ) * 100;

        return view('orders.create', compact('products', 'customers', 'tax'));
    }

    public function createStepOne()
    {
        abort_if(Gate::denies('order_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $products = Product::all();
        $salesrep = auth()->user();

        if ($salesrep->IsSalesperson == '1'){
            $customers = DB::table('customers')->where('SalesRepID', auth()->user()->RepCode)->pluck('CustomerName', 'acc_main')->prepend(trans('global.pleaseSelect'), '');
        } else {
            $customers = Customer::all()->pluck('CustomerName', 'acc_main')->prepend(trans('global.pleaseSelect'), '');
        }

        return view('orders.create', compact('products', 'customers'));
    }

    public function postCreateStepOne(StoreOrderRequest $request)
    {
        $orderData = $request->order;

        if (empty($request->session()->get('order'))) {
            $order = new Order();
            $order->fill($orderData);
            $request->session()->put('order', $order);
        } else {
            $order = $request->session()->get('order');
            $order->fill($orderData);
            $request->session()->put('order', $order);
        }

        return redirect()->route('orders.create.step.two');
    }

    public function createStepTwo(StoreOrderRequest $request)
    {
        $order = $request->session()->get('order');

        $matchCustomer = $order->CustomerID;

        $specialDeal = SpecialDeals::where('CustomerID', "=", $matchCustomer)
            ->whereDate('StartDate', '<=', Carbon::today()->toDateString())
            ->whereDate('EndDate', '>=', Carbon::today()->toDateString())
            ->get('StockItemID')
            ->pluck('StockItemID');

        $dealDate = Carbon::today()->toDateString();

        if(count($specialDeal) > 0){

            $products = DB::select(DB::raw("SELECT DISTINCT products.id, products.StockItemName, products.StockCode, products.SellingPrice,
            products.DiscountPercentage AS Discount, products.TaxRateID, special_deals.StartDate, special_deals.EndDate, special_deals.DiscountPercentage, special_deals.UnitPrice
            FROM products
            LEFT OUTER JOIN special_deals on products.StockCode = special_deals.StockItemID
            AND special_deals.CustomerID = '$matchCustomer'
            AND '$dealDate' BETWEEN special_deals.StartDate AND special_deals.EndDate
            ORDER BY products.StockItemName"));

        } else {
            //$products = Product::all();
            $products = Product::select("*")
                ->where("status", 1)
                ->orderBy("StockItemName")
                ->get();
        }

        $creditStatus = DB::table('customers')->where('acc_main', $matchCustomer)->first()->IsOnCreditHold;
        $tax = VatCalculator::getTaxRateForLocation( 'ZA' ) * 100;

        //echo '<pre>', print_r($creditStatus,1); die;
        //dd($creditStatus);

        return view('orders.createStep2', compact('order', $order, 'products', 'tax', 'creditStatus'));

    }

    public function getprice($product_id)
    {
        //$unitprice = Product::where('id', $product_id)->first();
        $unitprice = Product::select('SellingPrice', 'DiscountPercentage')
            ->where('id', $product_id)
            ->first();
        //dump($unitprice);
        return response()->json($unitprice, 200);

    }

    public function postCreateStepTwo(StoreOrderRequest $request)
    {
        $orderData = $request->session()->get('order');

        //echo "<pre>"; print_r($orderData); die;
        $order = Order::create([
            'CustomerID'    => $orderData->CustomerID,
            'SalesPersonID' => $orderData->SalesPersonID,
            'OrderStatusID' => $orderData->OrderStatusID,
            'OrderNumber'   => $orderData->OrderNumber,
            'LastEditedBy'  => $orderData->LastEditedBy,
            'CustomerPurchaseOrderNumber'   => $orderData->CustomerPurchaseOrderNumber,
            'OrderDate'     => $orderData->OrderDate,
            'Authorisation' => $orderData->Authorisation,
        ]);

        for ($i=0; $i < count($request->StockItem); $i++) {
            if (isset($request->Quantity[$i]) && isset($request->UnitPrice[$i])){

                OrdersItem::create([
                    'OrderID'      => $order->id,
                    'StockItem'    => $request->StockItem[$i],
                    'Quantity'     => $request->Quantity[$i],
                    'UnitPrice'    => $request->UnitPrice[$i],
                    'TaxRate'      => $request->TaxRate[$i],
                    //'LastEditedBy' => $request->LastEditedBy[$i]
                    'LastEditedBy' => $orderData->LastEditedBy
                ]);
            }
        }

        return redirect()->route('orders.index');
    }

    public function store(StoreOrderRequest $request)
    {
        $order = Order::create($request->order);

       //echo "<pre>"; print_r($order); die;

        for ($i=0; $i < count($request->StockItem); $i++) {
            if (isset($request->Quantity[$i]) && isset($request->UnitPrice[$i])){

                OrdersItem::create([
                    'OrderID'      => $order->id,
                    'StockItem'    => $request->StockItem[$i],
                    'Quantity'     => $request->Quantity[$i],
                    'UnitPrice'    => $request->UnitPrice[$i],
                    'TaxRate'      => $request->TaxRate[$i],
                    'LastEditedBy' => $request->LastEditedBy[$i]
                ]);
            }
        }

        return redirect()->route('orders.index');
    }

    public function edit(Order $order)
    {
        abort_if(Gate::denies('order_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $products = Product::all();
        $customers = Customer::all();
        $order->load('products');
        $order->load('customers');

        return view('orders.edit', compact('products', 'customers', 'order'));
    }

    public function update(UpdateOrderRequest $request, Order $order)
    {
        $order->update($request->all());

        $order->products()->detach();
        $products = $request->input('products', []);
        $quantities = $request->input('quantities', []);
        for ($product=0; $product < count($products); $product++) {
            if ($products[$product] != '') {
                $order->products()->attach($products[$product], ['quantity' => $quantities[$product]]);
            }
        }

        return redirect()->route('orders.index');
    }

    public function show(Order $order)
    {
        abort_if(Gate::denies('order_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $order->load('orderItems');
        $order->load('customer');

        //$pdf = PDF::loadView('orders.show', compact('order'));
        //return $pdf->stream('pdfview.pdf');

        return view('orders.show', compact('order'));
    }

    public function destroy(Order $order)
    {
        abort_if(Gate::denies('order_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $order->delete();

        return back();
    }

    public function massDestroy(MassDestroyOrderRequest $request)
    {
        Order::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function downloadOrder(Order $order)
    {

        Order::where('id', $order->id)
            ->update(['LastEditedBy' => auth()->user()->id, 'OrderStatusID' => '2', 'updated_at' => Carbon::now()->toDateTimeString()]);

        $document = new \DOMDocument("1.0", "UTF-8");
        $document -> appendChild($document->createElement('OrderMessage'));

        $rootTag = $document->documentElement;
        $headerTag = $rootTag->appendChild(
            $document->createElement("StandardBusinessDocumentHeader")
        );
        $senderTag = $headerTag->appendChild(
            $document->createElement("Sender")
        );
        $senderTag
            ->appendChild($document->createElement("Identifier"))
            ->appendChild($document->createTextNode($order->customer->StoreEAN));

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
            ->appendChild($document->createTextNode($order->OrderNumber));
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
        $orderTag ->appendChild($document->createAttribute("xmlns"));
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
            ->appendChild($document->createTextNode("$order->CustomerPurchaseOrderNumber"));
        $orderTag
            ->appendChild($document->createElement("orderTypeCode"))
            ->appendChild($document->createTextNode("220"));

        $orderTag
            ->appendChild($document->createElement("additionalOrderInstruction"))
            ->appendChild($document->createTextNode(""));
        $orderbuyerTag = $orderTag->appendChild(
            $document->createElement("buyer")
        );
        $orderbuyerTag
            ->appendChild($document->createElement("gln"))
            ->appendChild($document->createTextNode($order->customer->StoreEAN));
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
        foreach ($order->orderItems as $key) {
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
                ->appendChild($document->createTextNode( number_format($key->Quantity * ($key->UnitPrice / (1+($key->TaxRate/100))), 2, ".", " ")));
            $orderlineitemTag
                ->appendChild($document->createElement("netPrice"))
                ->appendChild($document->createTextNode( number_format(($key->Quantity * $key->UnitPrice), 2, ".", " " )));
            $orderlineitemTag
                ->appendChild($document->createElement("monetaryAmountExcludingTaxes"))
                ->appendChild($document->createTextNode( number_format($key->UnitPrice / (1+($key->TaxRate/100)), 2, ".", " ")));
            $orderlineitemTag
                ->appendChild($document->createElement("monetaryAmountIncludingTaxes"))
                ->appendChild($document->createTextNode( number_format( ($key->UnitPrice), 2, ".", " ") ));

            $ordertradeitemTag = $orderlineitemTag->appendChild(
                $document->createElement("transactionalTradeItem")
            );
            /*$ordertradeitemTag
                ->appendChild($document->createElement("gtin"))
                ->appendChild($document->createTextNode($key->product->Barcode));*/
            $ordertradeitemTag
                ->appendChild($document->createElement("gtin"))
                ->appendChild($document->createTextNode( empty($key->product->Barcode) ? $key->product->StockCode : $key->product->Barcode ));
            $ordertradeitemTag
                ->appendChild($document->createElement("additionalTradeItemIdentification"))
                ->appendChild($document->createTextNode("" ));
            $ordertradeitemTag
                ->appendChild($document->createElement("additionalTradeItemIdentification"))
                ->appendChild($document->createTextNode($key->product->StockCode ));
            $ordertradeitemTag
                ->appendChild($document->createElement("additionalTradeItemIdentification"))
                ->appendChild($document->createTextNode($key->product->StockItemName ));
            $ordertradeitemTag
                ->appendChild($document->createElement("tradeItemDescription"))
                ->appendChild($document->createTextNode($key->product->StockItemName));

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
                ->appendChild($document->createTextNode($order->customer->StoreEAN));
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

        $document->formatOutput = TRUE;
        header('Content-type: "application/force-download"; charset="utf8"');
        header('Content-disposition: attachment; filename="Order'.$order->OrderNumber.'.SCO"');
        echo $document->saveXML();

    }

    public function productSearch(Request $request)
    {
        $todayDate = date('Y-M-d');
        $product_code = explode("(", $request['data']);
        $product_code[0] = rtrim($product_code[0], " ");
        $order_product_data = Product::where([
            ['StockCode', $product_code[0]],
            ['status', 1]
        ])->first();
    }
}
