<?php

namespace App\Services;

use App\DataTransferObjects\ProductData;
use App\Models\Product;
use Illuminate\Support\Str;

class ProductService
{
    /**
     * @param  MediaService  $mediaService  Service for handling product media.
     * @param  ProductStockService  $productStockService  Service for managing variant stocks.
     */
    public function __construct(
        protected MediaService $mediaService,
        protected ProductStockService $productStockService
    ) {}

    /**
     * Create a new product in the database.
     */
    public function store(ProductData $data): Product
    {
        $product = Product::create([
            'name' => $data->name,
            'slug' => $data->slug ?: $this->ensureUniqueSlug(Str::slug($data->name)),
            'user_id' => $data->user_id ?? auth()->id(),
            'added_by' => $data->added_by,
            'brand_id' => $data->brand_id,
            'description' => $data->description,
            'published' => $data->published,
            'approved' => $data->approved,
            'shipping_type' => $data->shipping_type,
            'shipping_cost' => $data->shipping_cost,
            'est_shipping_days' => $data->est_shipping_days,
            'meta_title' => $data->meta_title ?: $data->name,
            'meta_description' => $data->meta_description ?: strip_tags($data->description ?? ''),
            'digital' => $data->digital,
            'wholesale_product' => $data->wholesale_product,
            'has_warranty' => $data->has_warranty,
            'extra_attributes' => $data->extra_attributes,
        ]);

        $product->categories()->sync($data->category_ids);

        if (! empty($data->tags)) {
            $product->attachTags($data->tags);
        }

        // Sync Variants and Stocks
        $this->productStockService->store($product, ...$data->stocks);

        return $product;
    }

    /**
     * Update an existing product.
     *
     * @param  ProductData  $data  The updated product data.
     * @param  Product  $product  The product model instance.
     */
    public function update(ProductData $data, Product $product): Product
    {
        $product->update([
            'name' => $data->name,
            'slug' => $data->slug ?: $product->slug,
            'brand_id' => $data->brand_id,
            'description' => $data->description,
            'published' => $data->published,
            'approved' => $data->approved,
            'shipping_type' => $data->shipping_type,
            'shipping_cost' => $data->shipping_cost,
            'est_shipping_days' => $data->est_shipping_days,
            'meta_title' => $data->meta_title ?: $data->name,
            'meta_description' => $data->meta_description ?: strip_tags($data->description ?? ''),
            'digital' => $data->digital,
            'wholesale_product' => $data->wholesale_product,
            'has_warranty' => $data->has_warranty,
            'extra_attributes' => $data->extra_attributes,
        ]);

        $product->categories()->sync($data->category_ids);

        $product->syncTags($data->tags ?? []);

        // Stocks Update
        $this->productStockService->store($product, ...$data->stocks);

        return $product;
    }

    /**
     * Delete a product and its related data.
     */
    public function destroy(int $id): bool
    {
        $product = Product::findOrFail($id);

        $product->categories()->detach();
        $product->stocks()->delete();
        $product->flash_deal_products()->delete();
        $product->reviews()->delete();
        $product->wishlists()->delete();
        $product->carts()->delete();

        return (bool) $product->delete();
    }

    /**
     * Duplicate a product and its related variants.
     *
     * @param  Product  $product  The source product to duplicate.
     * @return Product The newly created duplicate product.
     */
    public function duplicate(Product $product): Product
    {
        $new_product = $product->replicate();
        $new_product->slug = $this->ensureUniqueSlug($product->slug);
        $new_product->save();

        // Duplicate relationships
        $new_product->categories()->sync($product->categories->pluck('id'));

        // Sync tags if they exist
        if (method_exists($product, 'tags')) {
            $new_product->syncTags($product->tags->pluck('name'));
        }

        // Duplicate Stocks
        $this->productStockService->product_duplicate_store($product->stocks, $new_product);

        return $new_product;
    }

    /**
     * Ensure the generated slug is unique in the products table.
     *
     * @param  string  $slug  The slug to check.
     * @param  int|null  $ignoreId  Optional ID to ignore (during updates).
     * @return string A unique slug.
     */
    protected function ensureUniqueSlug(string $slug, ?int $ignoreId = null): string
    {
        $originalSlug = $slug;
        $count = 1;

        while (Product::where('slug', $slug)
            ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->exists()
        ) {
            $slug = $originalSlug.'-'.$count++;
        }

        return $slug;
    }
}
