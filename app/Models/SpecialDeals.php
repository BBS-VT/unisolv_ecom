<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SpecialDeals extends Model
{
    public $table = 'special_deals';

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected $fillable = [
        'StockItemID',
        'CustomerID',
        'BuyingGroupID',
        'CustomerCategoryID',
        'StockGroupID',
        'DealDescription',
        'StartDate',
        'EndDate',
        'DiscountAmount',
        'DiscountPercentage',
        'UnitPrice',
        'LastEditedBy',
    ];

    public function products()
    {
        return $this->belongsTo(Product::class, 'StockItemID', 'StockCode' );
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'CustomerID', 'acc_main');
    }

    public function buyingGroup()
    {
        return $this->belongsTo(BuyingGroup::class, 'BuyingGroupID', 'id');
    }

    public function customerGroup()
    {
        return $this->belongsTo(CustomerCategory::class, 'CustomerCategoryID', 'AccountType');
    }

    public function productCategory()
    {
        return $this->belongsTo(ProductCategory::class, 'StockGroupID', 'id');
    }

    public function lastEdited()
    {
        return $this->belongsTo(User::class, 'LastEditedBy', 'id');
    }
}
