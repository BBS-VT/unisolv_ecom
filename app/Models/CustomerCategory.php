<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerCategory extends Model
{
    public $table = 'customer_categories';

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected $fillable = [
        'company_id',
        'AccountType',
        'CustomerCategoryName',
        'ValidFrom',
        'ValidTo',
        'LastEditedBy',
    ];

    public function lastedited()
    {
        return $this->hasOne('User', 'LastEditedBy');
    }

    public function specialdeals()
    {
        return $this->hasMany(SpecialDeals::class, 'AccountType', 'CustomerCategoryID');
    }

    public function customers()
    {
        return $this->hasMany(Customer::class, 'AccountType', 'CustomerCategoryID');
    }

    /**
     * Define Relation with Company Model
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * List Customer Categories for Select2
     *
     * @return json
     */
    public function getSelect2Array($company_id)
    {
        return self::findByCompany($company_id)
            ->select('id', 'CustomerCategoryName AS text')
            ->get();
    }

    /**
     * Scope a query to only include Customer Categories of a given company
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $company_id
     *
     * @return \Illuminate\Database\Eloquent\Builder
     *
     */
    public function scopeFindByCompany($query, int $company_id)
    {
        $query->where('company_id', $company_id);
    }
}
