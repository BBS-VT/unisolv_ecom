@extends('layouts.print')

@push('style')

@endpush

@section('content')
    <!-- end page title end breadcrumb -->
    <div class="row">
        <div class="col-lg-10 mx-auto">
            <div class="card">
                <div class="card-body invoice-head">
                    <div class="row">
                        <div class="col-md-4 align-self-center">
                            <!--                                    <img src="assets/images/logo-sm.png" alt="logo-small" class="logo-sm mr-1" height="24">
                                                                <img src="assets/images/logo-dark.png" alt="logo-large" class="logo-lg logo-dark" height="20">
                                                                <img src="assets/images/logo.png" alt="logo-large" class="logo-lg logo-light" height="20">-->
                            <p class="mt-2 mb-0 text-muted">Quenera Distribution Pty (LTD)</p>
                        </div><!--end col-->
                        <div class="col-md-8">

                            <ul class="list-inline mb-0 contact-detail float-right">
                                <li class="list-inline-item">
                                    <div class="pl-3">
                                        <i class="mdi mdi-web"></i>
                                        <p class="text-muted mb-0">quenera@mjpress.co.za</p>
                                        <p class="text-muted mb-0">www.qrstuvwxyz.com</p>
                                    </div>
                                </li>
                                <li class="list-inline-item">
                                    <div class="pl-3">
                                        <i class="mdi mdi-phone"></i>
                                        <p class="text-muted mb-0">043 743 4557</p>
                                        <p class="text-muted mb-0">043 743 4557</p>
                                    </div>
                                </li>
                                <li class="list-inline-item">
                                    <div class="pl-3">
                                        <i class="mdi mdi-map-marker"></i>
                                        <p class="text-muted mb-0">1 Strelitzia St</p>
                                        <p class="text-muted mb-0">Braelyn, East London, 5201</p>
                                    </div>
                                </li>
                            </ul>
                        </div><!--end col-->
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-1">

                        </div>
                        <div class="col-md-4">
                            <div class="float-left">
                                <address class="font-13">
                                    <strong class="font-14">Invoice To :</strong><br>
                                    {{ $order->customer->CustomerName }}<br>
                                    {{ $order->customer->PostalAddressLine1 }}<br>
                                    {{ $order->customer->PostalAddressLine2 }}<br>
                                    {{ $order->customer->PostalCity }}, {{ $order->customer->PostalPostalCode }}<br>
                                    <abbr title="Phone">Ph:</abbr> {{ $order->customer->PhoneNumber }}
                                </address>
                            </div>
                        </div><!--end col-->
                        <div class="col-md-4">
                            <div class="">
                                <address class="font-13">
                                    <strong class="font-14">Deliver To:</strong><br>
                                    {{ $order->customer->CustomerName }}<br>
                                    {{ $order->customer->DeliveryAddressLine1 }}<br>
                                    {{ $order->customer->DeliveryAddressLine2 }}<br>
                                    {{ $order->customer->DeliveryCity }}, {{ $order->customer->DeliveryPostalCode }}<br>
                                    <abbr title="Phone">Ph:</abbr> {{ $order->customer->PhoneNumber }}
                                </address>
                            </div>
                        </div>
                        <div class="col-md-3 ">
                            <div class="font-13 float-right">
                                <strong class="font-14">Account:</strong>&nbsp; {{ $order->customer->acc_main }}<br>
                                <strong class="font-14">Order Date :</strong> {{ $order->created_at->format('d/m/Y') }}<br>
                                <strong class="font-14">Order Nr :</strong> {{ $order->OrderNumber }}<br>
                                <strong class="font-14">Rep :</strong> {{ $order->customer->salesrep->RepCode }}<br>
                            </div>
                        </div><!--end col-->
                    </div><!--end row-->

                    <div class="row">
                        <div class="col-lg-12">
                            <div class="table-responsive project-invoice">
                                <table class="table table-bordered mb-0">
                                    <thead class="thead-light">
                                    <tr>
                                        <th>Stock Code</th>
                                        <th>Description</th>
                                        <th class="text-center">Qty</th>
                                        <th class="text-right">U/Price(Ex)</th>
                                        <th class="text-right">Unit Tax</th>
                                        <th class="text-right">Net Price</th>
                                        <th class="text-right">Total(Incl)</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($order->items as $key)
                                        <tr>
                                            <td>
                                                <p class="mb-0 text-muted">{{ $key->product->StockCode }}</p>
                                            </td>
                                            <td>
                                                {{-- <h5 class="mt-0 mb-1 font-14">{{ $key->product->StockItemName }}</h5>--}}
                                                <p class="mb-0 text-muted">{{ $key->product->StockItemName }}</p>
                                            </td>
                                            <td class="text-center">{{ $key->Quantity }}</td>
                                            <td class="text-right">{{ number_format(($key->UnitPrice / 1.15) / 100, 2, ".", " ") }}</td>
                                            <td class="text-right">{{ number_format(($key->UnitPrice - ($key->UnitPrice / 1.15)) / 100, 2, ".", " ") }}</td>
                                            <td class="text-right">{{ number_format($key->UnitPrice / 100, 2, ".", " ") }}</td>
                                            <td class="text-right">{{ number_format(($key->Quantity * $key->UnitPrice) / 100, 2, ".", " ") }}</td>
                                        </tr>
                                    @endforeach

                                    <tr >
                                        <td colspan="5" class="border-0"></td>
                                        <td class="border-0 font-14 text-dark text-right"><b>Sub Total</b></td>
                                        <td class="border-0 font-14 text-dark text-right"><b>{{ number_format($order->getSubTotalAmountIncl() / 100, 2, ".", " ") }}</b></td>
                                    </tr>
                                    <tr>
                                        <th colspan="5" class="border-0"></th>
                                        <td class="border-0 font-14 text-dark text-right"><b>VAT @ 15%</b></td>
                                        <td class="border-0 font-14 text-dark text-right"><b>{{ number_format($order->getTotalVATInclAmount() / 100, 2, ".", " ") }}</b></td>
                                    </tr>
                                    <tr class="bg-black text-white">
                                        <th colspan="5" class="border-0"></th>
                                        <td class="border-0 font-14 text-right"><b>Total</b></td>
                                        <td class="border-0 font-14 text-right"><b>{{ number_format($order->getSubTotalAmountIncl() / 100, 2, ".", " ") }}</b></td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="row justify-content-center">
                        <div class="col-lg-6">
                            <h5 class="mt-4">Terms And Conditions :</h5>
                            <ul class="pl-3">
                                <br>
                            </ul>
                        </div> <!--end col-->
                        <div class="col-lg-6 align-self-end">
                            <div class="float-right" style="width: 30%;">
                                <small>Received By:</small>
                                <img src="assets/images/signature.png" alt="" class="mt-2 mb-1" height="26">
                                <br>
                                <br>
                                <p class="border-top">Signature</p>
                            </div>
                        </div><!--end col-->
                    </div><!--end row-->
                    <hr>
                    <div class="row d-flex justify-content-center">
                        <div class="col-lg-12 col-xl-4 ml-auto align-self-center">
                            <div class="text-center"><small class="font-12">Thank you very much for doing business with us.</small></div>
                        </div><!--end col-->
                        <div class="col-lg-12 col-xl-4">
                            <div class="float-right d-print-none">
                                <a href="javascript:window.print()" class="btn btn-soft-info btn-sm">Print</a>
                                <!--                                        <a href="#" class="btn btn-soft-primary btn-sm">Submit</a>
                                                                        <a href="#" class="btn btn-soft-danger btn-sm">Cancel</a>-->
                            </div>
                        </div><!--end col-->
                    </div><!--end row-->
                </div><!--end card-body-->
            </div><!--end card-->
        </div><!--end col-->
    </div><!--end row-->


@endsection

@push('custom-scripts')

@endpush

