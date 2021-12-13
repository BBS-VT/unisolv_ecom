<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PackageType extends Model
{
    public $table = 'package_types';

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected $fillable = [
        'PackageTypeName',
        'company_id',
        'LastEditedBy'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'UnitPackageID', 'id');
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
     * List Product Units for Select2 Javascript Library
     *
     * @return json
     */
    public static function getSelect2Array($company_id) {
        // return
        return self::findByCompany($company_id)
            ->select('id', 'name AS text')
            ->get();
    }

    /**
     * Scope a query to only include Product Units of a given company.
     *
     * @param \Illuminate\Database\Eloquent\Builder  $query
     * @param int $company_id
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFindByCompany($query, $company_id)
    {
        $query->where('company_id', $company_id);
    }
}
