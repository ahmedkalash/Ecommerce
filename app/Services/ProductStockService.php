<?php

namespace App\Services;

use App\DataTransferObjects\ProductStockData;
use App\Models\Product;

/**
 * Handles the persistence of product variants (stocks).
 *
 * This service is exclusively DTO-driven and focused on the modern workflow.
 */
class ProductStockService
{
    /**
     * Store or update product stocks for a given product.
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

            // Handle Wholesale Prices
            $this->saveWholesalePrices($stock, $stockData->wholesale_prices);
        }

        // Cleanup: Remove any variants that weren't present in the provided DTO list.
        $product->stocks()->whereNotIn('id', $processedIds)->delete();
    }

    /**
     * Save wholesale prices for a specific stock.
     *
     * @param  \App\Models\ProductStock  $stock
     * @param  \App\DataTransferObjects\WholesalePriceData[]  $wholesalePrices
     */
    protected function saveWholesalePrices($stock, array $wholesalePrices): void
    {
        $stock->wholesalePrices()->delete();

        foreach ($wholesalePrices as $priceData) {
            $stock->wholesalePrices()->create([
                'min_qty' => $priceData->min_qty,
                'max_qty' => $priceData->max_qty,
                'price' => $priceData->price,
            ]);
        }
    }

    /**
     * Replicates stocks for a duplicate product.
     *
     * @param  iterable<\App\Models\ProductStock>  $stocks
     */
    public function product_duplicate_store(iterable $stocks, Product $new_product): void
    {
        foreach ($stocks as $stock) {
            $new_stock = $stock->replicate();
            $new_stock->product_id = $new_product->id;
            $new_stock->save();

            // Replicate Wholesale Prices
            foreach ($stock->wholesalePrices as $wholesalePrice) {
                $new_wholesalePrice = $wholesalePrice->replicate();
                $new_wholesalePrice->product_stock_id = $new_stock->id;
                $new_wholesalePrice->save();
            }
        }
    }
}
