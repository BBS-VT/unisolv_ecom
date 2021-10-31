<?php


namespace App\Http\View\Presenters;

use App\Models\Product;
use App\Models\Products;
use Cknow\Money\Money;
use Psy\VarDumper\Presenter;


class PricePresenter extends Presenter
{
    protected $product;

    public function __construct(Product $product)
    {
        $this->product = $product;
    }

    public function priceFormatted()
    {
        return Money::formatAmount($this->product->price);
    }
}
