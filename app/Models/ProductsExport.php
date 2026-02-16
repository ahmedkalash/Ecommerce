<?php

namespace App\Models;

use App\Traits\PreventDemoModeChanges;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ProductsExport implements FromCollection, WithHeadings, WithMapping
{
    use PreventDemoModeChanges;

    public function collection()
    {
        return Product::all();
    }

    public function headings(): array
    {
        return [
            'name',
            'description',
            'added_by',
            'user_id',
            'category_id',
            'brand_id',
            'video_provider',
            'video_link',
            'unit_price',

            'current_stock',
            'est_shipping_days',
            'meta_title',
            'meta_description',
        ];
    }

    /**
     * @var Product
     */
    public function map($product): array
    {
        $qty = 0;
        foreach ($product->stocks as $key => $stock) {
            $qty += $stock->qty;
        }

        return [
            $product->name,
            $product->description,
            $product->added_by,
            $product->user_id,
            $product->categories->pluck('id')->implode(', '),
            $product->brand_id,
            $product->video_provider,
            $product->video_link,
            $product->unit_price,

            $qty,
            $product->est_shipping_days,
            $product->meta_title,
            $product->meta_description,
        ];
    }
}
