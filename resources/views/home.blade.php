@extends('layouts.front')

@section('content')


    <div class="container">
        <div class="row">
            <div class="col-md-3">
                <section class="panel">
                    <div class="panel-body">
                        <input type="text" placeholder="search" class="form-control" />
                    </div>
                </section>
                <section class="panel">
                    <header class="panel-heading">
                        {{ trans('cruds.productCategory.title') }}
                    </header>
                    <div class="panel-body">
                        <ul class="nav prod-cat">
                            <li>
                                @foreach($categories as $category)
                                    <a href="{{ route('catalog', ['category' => $category->id]) }}" class="list-group-item {{ request()->input('category') == $category->id ? ' active' : '' }}">
                                        {{ $category->StockGroupName }}
                                    </a>
                                @endforeach
                            </li>
                        </ul>
                    </div>
                </section>
            </div>
            <div class="col-md-9">
                <section class="panel">
                    <div class="panel-body">
                        <div class="float-right">
                            {{ $products->appends(request()->query())->links() }}
                        </div>
                    </div>
                </section>
                <div class="row product-list">
                    @foreach($products as $product)
                        <div class="col-md-4">
                            <section class="panel">
                                <div class="pro-img-box">
                                    <img class="card-img-top" src="{{ $product->photo ? $product->photo->thumbnail : 'https://via.placeholder.com/500x280&text=Image' }}" alt="">
                                    <a href="#"></a>
                                </div>

                                <div class="panel-body text-center">
                                    <h4>
                                        <a href="#" class="pro-title">{{ $product->StockItemName }}</a>
                                    </h4>
                                    <p class="card-text">{{ Str::limit($product->MarketingComments, 50) }}</p>
                                </div>
                            </section>
                        </div>
                    @endforeach
                </div>
                <div class="row">
                    {{ $products->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
