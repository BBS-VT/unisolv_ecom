<div class="col-lg-6">
    <div class="card">
        <div class="card-header">
            <div class="row align-items-center">
                <div class="col">
                    <h4 class="card-title">Frequently Reordered Products</h4>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead class="thead-light">
                    <tr>
                        <th class="border-top-0">Product</th>
                        <th class="border-top-0">Orders</th>
                        <th class="border-top-0">Unique Customers</th>
                        <th class="border-top-0">Reorder Ratio</th>
                        <th class="border-top-0">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    {{--@forelse($productReorderRates as $product)
                        <tr>
                            <td>
                                <div class="media">
                                    <img src="{{ asset('images/products/01.png') }}" height="30" class="mr-3 align-self-center rounded" alt="...">
                                    <div class="media-body align-self-center">
                                        <h6 class="m-0">{{ $product->StockItemName }}</h6>
                                        <a href="#" class="font-12 text-primary">ID: {{ $product->StockCode }}</a>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $product->order_count }}</td>
                            <td>{{ $product->unique_customers }}</td>
                            <td>
                                <div class="progress" style="height: 5px;">
                                    <div class="progress-bar bg-success" role="progressbar"
                                         style="width: {{ min($product->reorder_ratio * 25, 100) }}%;"
                                         aria-valuenow="{{ $product->reorder_ratio }}"
                                         aria-valuemin="0" aria-valuemax="5"></div>
                                </div>
                                <span class="font-12">{{ number_format($product->reorder_ratio, 1) }} orders per customer</span>
                            </td>
                            <td>
                                <a href="{{ route('products.show', $product->id) }}" class="mr-2">
                                    <i class="las la-eye text-success font-18"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">No reordered products found in this period</td>
                        </tr>
                    @endforelse--}}
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
