<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
class PackSizeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'stock_code' => $this->StockCode,
            'name' => $this->StockItemName,
            'description' => $this->description,
            'pack_size' => $this->Packsize,
            'refer_code' => $this->refer_code,
            'is_root' => !$this->refer_code,

            // Stock information
            'stock_holding' => $this->whenLoaded('stockHolding', function () {
                return [
                    'quantity' => $this->stockHolding->QuantityOnHand,
                    'cost_price' => $this->stockHolding->LastCostPrice,
                    'selling_price' => $this->SellingPrice,
                    'last_updated' => $this->stockHolding->updated_at,
                ];
            }),

            // Calculated fields
            'available_packs' => $this->available_packs,
            'effective_price' => $this->effective_price,
            'total_base_units' => $this->total_base_units,
            'max_packs_from_base_units' => $this->max_packs_from_base_units,

            // Pack size family information
            'pack_size_family' => $this->when($request->include_family, function () {
                return PackSizeResource::collection($this->packSizeFamily()->with('stockHolding')->get());
            }),

            // Related products
            'referred_product' => $this->whenLoaded('referredProduct', function () {
                return new PackSizeResource($this->referredProduct);
            }),

            'referring_products' => $this->whenLoaded('referringProducts', function () {
                return PackSizeResource::collection($this->referringProducts);
            }),

            // Metadata
            'category' => $this->whenLoaded('category', function () {
                return [
                    'id' => $this->category->id,
                    'name' => $this->category->name,
                ];
            }),

            'supplier' => $this->whenLoaded('supplier', function () {
                return [
                    'id' => $this->supplier->id,
                    'name' => $this->supplier->name,
                ];
            }),

            'is_active' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }

}
