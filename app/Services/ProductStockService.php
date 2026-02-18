<?php

namespace App\Services;

use App\DataTransferObjects\ProductStockData;
use App\Exceptions\Redirectingexception;
use App\Models\Product;
use App\Models\ProductStock;

/**
 * Handles the persistence of product variants (stocks).
 *
 * This service is exclusively DTO-driven and focused on the modern workflow.
 */
class ProductStockService
{
    /**
     * Store or update product stocks for a given product.
     *
     * Synchronizes the database variants with the provided DTO list,
     * removing any variants that are no longer present in the input.
     *
     * @param  Product  $product  The product model.
     * @param  ProductStockData  ...$stocks  Variadic list of variant DTOs.
     */
    public function store(Product $product, ProductStockData ...$stocks): void
    {
        $processedIds = [];

        foreach ($stocks as $stockData) {
            $stock = $product->stocks()->updateOrCreate(
                ['variant' => $stockData->variant],
                [
                    'sku' => $stockData->sku,
                    'price' => $stockData->price,
                    'qty' => $stockData->qty,
                    'min_qty' => $stockData->min_qty,
                    'cash_on_delivery' => $stockData->cash_on_delivery,
                    'todays_deal' => $stockData->todays_deal,
                    'special_price' => $stockData->special_price,
                    'special_price_type' => $stockData->special_price_type,
                    'special_price_start' => $stockData->special_price_start,
                    'special_price_end' => $stockData->special_price_end,
                    'extra_attributes' => $stockData->extra_attributes,
                ]
            );

            $processedIds[] = $stock->id;
        }

        // Cleanup: Remove any variants that weren't present in the provided DTO list.
        $product->stocks()->whereNotIn('id', $processedIds)->delete();
    }

    /**
     * Replicates stocks for a duplicate product.
     *
     * Iterates through existing stocks and replicates them for the new product.
     *
     * @param  iterable<ProductStock>  $stocks  The collection of stocks to duplicate.
     * @param  Product  $new_product  The target product for the duplicated stocks.
     *
     * @throws Redirectingexception
     */
    public function product_duplicate_store(iterable $stocks, Product $new_product): void
    {
        foreach ($stocks as $stock) {
            $new_stock = $stock->replicate();
            $new_stock->product_id = $new_product->id;
            $new_stock->save();
        }
    }
}
