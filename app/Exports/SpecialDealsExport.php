<?php

namespace App\Exports;

use App\Models\SpecialDeals;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\FromCollection;

class SpecialDealsExport implements FromCollection, WithHeadings, WithMapping
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return SpecialDeals::all();
    }

    public function headings(): array
    {
        return [
            'Stock Code',
            'Description',
            'Account Code',
            'Customer',
            'Buying Group',
            'Customer Group',
            'Stock Department',
            'Deal Description',
            'Start Date',
            'End Date',
            'Discount Amount',
            'Discount Percentage',
            'Unit Price',
            'Last Edited By',
        ];
    }

    public function map($specialdeal): array
    {
        return [
            $specialdeal->products->StockCode,
            $specialdeal->products->StockItemName,
            $specialdeal->customer->acc_main,
            $specialdeal->customer->CustomerName ?? 0,
            $specialdeal->buyingGroup->BuyingGroupName ?? 0,
            $specialdeal->customerGroup->CustomerCategoryName ?? 0,
            $specialdeal->productCategory->StockGroupName ?? 0,
            $specialdeal->DealDescription,
            $specialdeal->StartDate,
            $specialdeal->EndDate,
            $specialdeal->DiscountAmount,
            $specialdeal->DiscountPercentage,
            $specialdeal->UnitPrice,
            $specialdeal->lastEdited->PreferredName,
        ];
    }

}
