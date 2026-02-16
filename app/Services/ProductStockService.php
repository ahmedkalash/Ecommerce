<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Facades\Log;

class ProductStockService
{
    /**
     * Store or update stocks for a given product.
     *
     * @param array{
     *     stocks?: array<int, array{
     *         variant: string,
     *         price?: float|string,
     *         qty?: int|string,
     *         sku?: string|null,
     *         min_qty?: int|string,
     *         cash_on_delivery?: bool|int,
     *         video_link?: string|null,
     *         video_provider?: string|null
     *     }>
     * } $data Raw input data containing 'stocks'
     * @param  Product  $product  The product record
     */
    public function store(array $data, Product $product): void
    {
        $stocksData = $data['stocks'] ?? [];

        if (empty($stocksData)) {
            Log::warning('ProductStockService::store called with empty stocks data', [
                'product_id' => $product->id,
            ]);

            return;
        }

        $processedIds = [];

        foreach ($stocksData as $stockData) {
            $variantName = $stockData['variant'] ?? '';

            $stock = $product->stocks()->firstOrNew(['variant' => $variantName]);

            $stock->price = (float) ($stockData['price'] ?? 0);
            $stock->qty = (int) ($stockData['qty'] ?? 0);
            $stock->sku = $stockData['sku'] ?? null;
            $stock->min_qty = (int) ($stockData['min_qty'] ?? 1);
            $stock->cash_on_delivery = (bool) ($stockData['cash_on_delivery'] ?? true);
            $stock->video_link = $stockData['video_link'] ?? null;
            $stock->video_provider = $stockData['video_provider'] ?? null;

            $stock->save();
            $processedIds[] = $stock->id;
        }

        // Remove stocks that are no longer part of the product definition
        $product->stocks()->whereNotIn('id', $processedIds)->delete();
    }

    /**
     * Replicate stocks for a duplicated product.
     *
     * @param  iterable  $stocks  Collection of ProductStock models
     * @param  Product  $new_product  The new product record
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
