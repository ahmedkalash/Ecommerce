<?php

namespace App\Services;

use App\DTOs\ProductData;
use App\Models\Product;
use Illuminate\Support\Str;

/**
 * Handles core product business logic: create, update, delete, duplicate.
 *
 * This service contains pure business logic only.
 * DB transactions, error handling, and logging are the caller's responsibility.
 */
class ProductService
{
    public function __construct(
        protected ProductStockService $productStockService
    ) {}

    /**
     * Create a new product with all relationships.
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
            'featured' => $data->featured,
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

        $product->categories()->sync($data->categories);

        if (! empty($data->tags)) {
            $product->attachTags($data->tags);
        }

        $this->productStockService->store($product, ...$data->stocks);

        return $product;
    }

    /**
     * Update an existing product and sync all relationships.
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
            'featured' => $data->featured,
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

        $product->categories()->sync($data->categories);
        $product->syncTags($data->tags ?? []);

        $this->productStockService->store($product, ...$data->stocks);

        return $product;
    }

    /**
     * Delete a product and all related data.
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
     */
    public function duplicate(Product $product): Product
    {
        $newProduct = $product->replicate();
        $newProduct->slug = $this->ensureUniqueSlug($product->slug);
        $newProduct->save();

        $newProduct->categories()->sync($product->categories->pluck('id'));

        if (method_exists($product, 'tags')) {
            $newProduct->syncTags($product->tags->pluck('name'));
        }

        $this->productStockService->product_duplicate_store($product->stocks, $newProduct);

        return $newProduct;
    }

    /**
     * Ensure the generated slug is unique in the products table.
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
