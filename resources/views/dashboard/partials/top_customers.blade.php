<!-- Add this card to your dashboard view -->
<div class="col-lg-6">
    <div class="card">
        <div class="card-header">
            <div class="row align-items-center">
                <div class="col">
                    <h4 class="card-title">Top Customers</h4>
                </div><!--end col-->
            </div>  <!--end row-->
        </div><!--end card-header-->
        <div class="card-body">
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead class="thead-light">
                    <tr>
                        <th class="border-top-0">Customer</th>
                        <th class="border-top-0">Orders</th>
                        <th class="border-top-0">Total Spent</th>
                        <th class="border-top-0">Avg. Order Value</th>
                        <th class="border-top-0">Action</th>
                    </tr><!--end tr-->
                    </thead>
                    <tbody>
                    @forelse($topCustomers as $customer)
                        <tr>
                            <td>
                                <div class="media">
                                    <div class="media-body align-self-center">
                                        <h6 class="m-0">{{ $customer->CustomerName }}</h6>
                                        <a href="#" class="font-12 text-primary">ID: {{ $customer->acc_main }}</a>
                                    </div><!--end media body-->
                                </div>
                            </td>
                            <td>{{ $customer->order_count }}</td>
                            <td>R{{ number_format($customer->total_spent, 2) }}</td>
                            <td>R{{ number_format($customer->avg_order_value, 2) }}</td>
                            <td>
                                <a href="{{ route('customers.show', $customer->id) }}" class="mr-2">
                                    <i class="las la-eye text-success font-18"></i>
                                </a>
                            </td>
                        </tr><!--end tr-->
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">No customers found in this period</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table> <!--end table-->
            </div><!--end /div-->
        </div><!--end card-body-->
    </div><!--end card-->
</div> <!--end col-->
