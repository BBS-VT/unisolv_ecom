<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'symbol',
        'precision',
        'thousand_separator',
        'decimal_separator',
        'swap_currency_symbol'
    ];

    /**
     * List Currencies for Select2 Javascript Library
     *
     * @return collect
     */
    public static function getSelect2Array() {
        $response = collect();
        foreach(self::all() as $currency){
            $response->push([
                'id' => $currency->id,
                'text' => "{$currency->code} - {$currency->name}"
            ]);
        }
        return $response;
    }
}
