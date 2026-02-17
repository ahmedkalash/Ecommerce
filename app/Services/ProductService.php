<?php

namespace App\Services;

use App\Enums\ShippingType;
use App\Models\Category;
use App\Models\Product;
use App\Models\SellerCategory;
use App\Models\Shop;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

// Added

class ProductService
{
    protected MediaService $mediaService;

    protected ProductStockService $productStockService;

    public function __construct(MediaService $mediaService, ProductStockService $productStockService) // Modified
    {
        $this->mediaService = $mediaService;
        $this->productStockService = $productStockService; // Added
    }

    /**
     * Store a newly created product in the database.
     *
     * @param array{
     *     name: string,
     *     slug?: string,
     *     brand_id?: int|string|null,
     *     categories?: int[]|string[],
     *     tags?: string|string[],
     *     description?: string|null,
     *     unit_price?: float|string,
     *     purchase_price?: float|string,
     *     discount?: float|string,
     *     discount_type?: string,
     *     current_stock?: int,
     *     shipping_type?: string,
     *     shipping_cost?: float|string,
     *     est_shipping_days?: int|null,
     *     meta_title?: string,
     *     meta_description?: string,
     *     published?: bool|int,
     *     has_warranty?: bool|int,
     *     thumbnail_img?: mixed,
     *     photos?: mixed,
     *     meta_img?: mixed,
     *     pdf?: mixed,
     *     colors?: string[],
     *     choice_no?: int[],
     *     choice_options?: array<int, array{name: string, values: string[]}>,
     *     stocks?: array<int, array{variant: string, price: float, sku: string, qty: int, image?: mixed}>
     * } $data Raw input data from Filament or Request
     *
     * @throws Exception
     * @throws \Throwable
     */
    public function store(array $data, bool $transaction = true): Product
    {
        $collection = collect($data);

        $user = auth('admin')->user() ?: auth('seller')->user();

        $user_id = $user->id;
        $added_by = $user->user_type;

        // Handle approved status (defaults to approved for admin panel)
        $approved = 1;

        // Tags processing
        $collection['tags'] = $this->formatTags($collection['tags'] ?? []);

        if (! isset($collection['meta_title']) || empty($collection['meta_title'])) {
            $collection['meta_title'] = $collection['name'];
        }
        if (! isset($collection['meta_description']) || empty($collection['meta_description'])) {
            $collection['meta_description'] = strip_tags($collection['description'] ?? '');
        }

        $shipping_cost = 0;
        if (isset($collection['shipping_type']) && $collection['shipping_type'] === 'flat_rate') {
            $shipping_cost = (float) ($collection['shipping_cost'] ?? 0);
        }

        $slug = $collection['slug'] ?? Str::slug($collection['name']);
        $slug = $this->ensureUniqueSlug($slug);

        $published = (int) ($collection['published'] ?? 1);

        $productData = [
            'name' => $collection['name'],
            'slug' => $slug,
            'user_id' => $user_id,
            'added_by' => $added_by,
            'brand_id' => $collection['brand_id'],
            'description' => $collection['description'] ?? null,
            'shipping_type' => $collection['shipping_type'] ?? ShippingType::FLAT_RATE->value,
            'shipping_cost' => $shipping_cost,
            'est_shipping_days' => $collection['est_shipping_days'] ?? null,
            'meta_title' => $collection['meta_title'] ?: $collection['name'],
            'meta_description' => $collection['meta_description'] ?: strip_tags((string) ($collection['description'] ?? '')),
            'published' => $published,
            'approved' => $approved,
            'has_warranty' => isset($collection['has_warranty']) && $collection['has_warranty'] ? 1 : 0,
        ];

        $product = Product::create($productData);

        // Tags processing via Spatie Tags
        if (isset($collection['tags'])) {
            $tagNames = is_array($collection['tags']) ? $collection['tags'] : array_filter(explode(',',
                $collection['tags']));
            $product->attachTags($tagNames);
        }

        // Sync Categories
        if (isset($collection['categories'])) {
            $product->categories()->sync($collection['categories']);
        }

        // Sync Media via Spatie Media Library
        $this->mediaService->syncMedia($product, $data, [
            'thumbnail_img' => 'thumbnail',
            'photos' => 'gallery',
            'meta_img' => 'meta',
            'pdf' => 'pdf',
            'short_video' => 'short_video',
            'short_video_thumbnail' => 'video_thumbnail',
        ]);

        // Sync Variants and Stocks
        $this->productStockService->store($data, $product);

        return $product;
    }

    /**
     * Update an existing product in the database.
     *
     * @param array{
     *     name?: string,
     *     slug?: string,
     *     brand_id?: int|string|null,
     *     categories?: int[]|string[],
     *     tags?: string|string[],
     *     description?: string|null,
     *     unit_price?: float|string,
     *     purchase_price?: float|string,
     *     discount?: float|string,
     *     discount_type?: string,
     *     current_stock?: int,
     *     shipping_type?: string,
     *     shipping_cost?: float|string,
     *     est_shipping_days?: int|null,
     *     meta_title?: string,
     *     meta_description?: string,
     *     published?: bool|int,
     *     has_warranty?: bool|int,
     *     thumbnail_img?: mixed,
     *     photos?: mixed,
     *     meta_img?: mixed,
     *     pdf?: mixed,
     *     colors?: string[],
     *     choice_no?: int[],
     *     choice_options?: array<int, array{name: string, values: string[]}>,
     *     stocks?: array<int, array{variant: string, price: float, sku: string, qty: int, image?: mixed}>
     * } $data Raw input data from Filament or Request
     * @param  Product  $product  The existing product model
     *
     * @throws Exception
     */
    public function update(array $data, Product $product): Product
    {
        $collection = collect($data);

        if (isset($collection['name'])) {
            $name = $collection['name'];
            $slug = $collection['slug'] ?? Str::slug($name);
            if ($slug !== $product->slug) {
                $product->slug = $this->ensureUniqueSlug($slug, $product->id);
            }
            $product->name = $name;
        }

        // Tags processing via Spatie Tags
        if (isset($collection['tags'])) {
            $tagNames = is_array($collection['tags']) ? $collection['tags'] : array_filter(explode(',',
                $collection['tags']));
            $product->syncTags($tagNames);
        }

        if (isset($collection['meta_title'])) {
            $product->meta_title = $collection['meta_title'] ?: $product->name;
        }
        if (isset($collection['meta_description'])) {
            $product->meta_description = $collection['meta_description'] ?: strip_tags((string) ($collection['description'] ?? $product->description ?? ''));
        }

        if (isset($collection['shipping_type'])) {
            $type = $collection['shipping_type'];
            $product->shipping_type = $type;
            if (isset($collection['shipping_cost'])) {
                $product->shipping_cost = ($type === ShippingType::FLAT_RATE->value) ? (float) $collection['shipping_cost'] : 0;
            }
        }

        if (isset($collection['description'])) {
            $product->description = $collection['description'];
        }
        if (isset($collection['categories'])) {
            $product->categories()->sync($collection['categories']);
        }
        if (isset($collection['brand_id'])) {
            $product->brand_id = $collection['brand_id'];
        }
        if (isset($collection['est_shipping_days'])) {
            $product->est_shipping_days = $collection['est_shipping_days'];
        }

        if (isset($collection['published'])) {
            $product->published = (int) $collection['published'];
        }
        if (isset($collection['approved'])) {
            $product->approved = (int) $collection['approved'];
        }

        if (isset($collection['has_warranty'])) {
            $product->has_warranty = (int) $collection['has_warranty'];
        }

        $product->save();

        // Sync Media
        $this->mediaService->syncMedia($product, $data, [
            'thumbnail_img' => 'thumbnail',
            'photos' => 'gallery',
            'meta_img' => 'meta',
            'pdf' => 'pdf',
            'short_video' => 'short_video',
            'short_video_thumbnail' => 'video_thumbnail',
        ]);

        // Stocks Update
        $this->productStockService->store($data, $product);

        return $product;
    }

    protected function formatTags(mixed $tags): string
    {
        if (is_array($tags)) {
            return implode(',', $tags);
        }

        if (is_string($tags) && ! empty($tags)) {
            // Check if it's JSON from Tagify (legacy)
            $decoded = json_decode($tags);
            if (is_array($decoded)) {
                return implode(',', array_column($decoded, 'value'));
            }

            return $tags;
        }

        return '';
    }

    protected function ensureUniqueSlug(string $slug, ?int $ignoreId = null): string
    {
        $originalSlug = $slug;
        $count = 1;

        while (Product::where('slug', $slug)->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))->exists()) {
            $slug = $originalSlug.'-'.$count++;
        }

        return $slug;
    }

    /**
     * Duplicate an existing product and its stocks.
     *
     * @param  Product  $product  The product to replicate
     *
     * @throws Exception
     */
    public function product_duplicate_store(Product $product): Product
    {
        $product_new = $product->replicate();
        $product_new->slug = $this->ensureUniqueSlug($product_new->slug.'-copy');
        $product_new->published = 0; // Draft by default

        // Explicitly check authorized guards (Admin/Seller) for duplication audit
        $user = auth('admin')->user() ?: auth('seller')->user();

        if ($user) {
            $product_new->user_id = $user->id;
            $product_new->added_by = $user->user_type;
        }

        $product_new->save();

        // Duplicate Stocks
        $this->productStockService->product_duplicate_store($product->stocks, $product_new);

        return $product_new;
    }

    /**
     * Permanently delete a product and its related records.
     *
     * @param  int  $id  Product ID
     *
     * @throws Exception
     */
    public function destroy(int $id): void
    {
        $product = Product::findOrFail($id);

        // Use relationship deletions if cascade isn't set in DB
        $product->product_translations()->delete();
        $product->stocks()->delete();
        $product->taxes()->delete();
        $product->wishlists()->delete();
        $product->carts()->delete();
        $product->frequently_bought_products()->delete();
        $product->last_viewed_products()->delete();
        $product->flash_deal_products()->delete();

        if (function_exists('deleteProductReview')) {
            deleteProductReview($product);
        }

        $product->delete();
    }

    /**
     * Search for products based on various criteria.
     *
     * @param  array  $data  Search criteria including category, product type, search key, etc.
     */
    public function product_search(array $data)
    {
        $collection = collect($data);
        $auth_user = auth()->user();
        $productType = $collection['product_type'];
        $products = Product::query();

        if ($collection['category'] != null) {
            $category = Category::with('childrenCategories')->find($collection['category']);
            $products = $category->products();
        }

        $products = in_array($auth_user->user_type, ['admin', 'staff']) ? $products->where(
            'products.added_by',
            'admin'
        ) : $products->where('products.user_id', $auth_user->id);
        $products->where('published', '1')->where('approved', '1');

        if ($productType == 'physical') {
            $products->where('digital', 0)->where('wholesale_product', 0);
        } elseif ($productType == 'digital') {
            $products->where('digital', 1);
        } elseif ($productType == 'wholesale') {
            $products->where('wholesale_product', 1);
        }

        if ($collection['product_id'] != null) {
            $products->where('id', '!=', $collection['product_id']);
        }

        if ($collection['search_key'] != null) {
            $products->where('name', 'like', '%'.$collection['search_key'].'%');
        }

        return $products->limit(20)->get();
    }

    public function setCategoryWiseDiscount(array $data)
    {
        // Original logic from file step 109
        try {
            $auth_user = auth()->user();
            $discount_start_date = null;
            $discount_end_date = null;
            $seller_discount_start_date = null;
            $seller_discount_end_date = null;
            $admin_discount_start_date = null;
            $admin_discount_end_date = null;

            if ($data['date_range'] != null) {
                $date_var = explode(' to ', $data['date_range']);
                $discount_start_date = strtotime($date_var[0]);
                $discount_end_date = strtotime($date_var[1]);
                $seller_discount_start_date = $discount_start_date;
                $seller_discount_end_date = $discount_end_date;
                $admin_discount_start_date = $discount_start_date;
                $admin_discount_end_date = $discount_end_date;
            }
            $seller_product_discount = isset($data['seller_product_discount']) ? $data['seller_product_discount'] : null;
            $admin_id = User::where('user_type', 'admin')->first()->id;

            $admin_discount = null;
            $seller_discount = null;

            $category = Category::find($data['category_id']);
            $products = Product::whereHas('categories', function ($q) use ($data) {
                $q->where('categories.id', $data['category_id']);
            });

            if (in_array($auth_user->user_type, ['admin', 'staff'])) {
                $admin_discount = $data['discount'];
                if ($seller_product_discount == 1) {
                    $shops = Shop::all();
                    foreach ($shops as $shop) {
                        $seller_cat = SellerCategory::where('category_id', $data['category_id'])
                            ->where('seller_id', $shop->user_id)
                            ->first();

                        if ($seller_cat) {
                            $seller_cat->update([
                                'discount' => $admin_discount,
                                'discount_start_date' => $admin_discount_start_date,
                                'discount_end_date' => $admin_discount_end_date,
                            ]);
                        } else {
                            SellerCategory::create([
                                'category_id' => $data['category_id'],
                                'seller_id' => $shop->user_id,
                                'discount' => $admin_discount,
                                'discount_start_date' => $admin_discount_start_date,
                                'discount_end_date' => $admin_discount_end_date,
                            ]);
                        }
                    }
                }

                if ($seller_product_discount == 0) {
                    $products->where('user_id', $admin_id);
                }
                // Category columns removed via migration
                /*
                $category->update([
                    'discount' => $admin_discount,
                    'discount_start_date' => $admin_discount_start_date,
                    'discount_end_date' => $admin_discount_end_date,
                ]);
                */
            } elseif ($auth_user->user_type == 'seller') {
                $products->where('user_id', $auth_user->id);
                $seller_discount = $data['discount'];
                $sellerCat = SellerCategory::where('seller_id', $auth_user->id)
                    ->where('category_id', $data['category_id'])
                    ->first();

                if ($sellerCat) {
                    $sellerCat->discount = $seller_discount;
                    $sellerCat->discount_start_date = $seller_discount_start_date;
                    $sellerCat->discount_end_date = $seller_discount_end_date;
                    $sellerCat->save();
                } else {
                    $sellerCat = new SellerCategory;
                    $sellerCat->seller_id = $auth_user->id;
                    $sellerCat->category_id = $data['category_id'];
                    $sellerCat->discount = $seller_discount;
                    $sellerCat->discount_start_date = $seller_discount_start_date;
                    $sellerCat->discount_end_date = $seller_discount_end_date;
                    $sellerCat->save();
                }
            }

            // Product level discount columns are removed.
            // In a real scenario, we should update discounts on all stocks of these products.
            foreach ($products->get() as $product) {
                $product->stocks()->update([
                    'discount' => $data['discount'],
                    'discount_type' => 'percent',
                ]);
            }

            return 1;
        } catch (Exception $e) {
            Log::error('Discount update failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => auth()->id(),
                'input_data' => $data,
            ]);

            return 0;
        }
    }
}
