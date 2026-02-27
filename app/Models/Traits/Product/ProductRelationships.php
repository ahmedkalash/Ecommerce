<?php

namespace App\Models\Traits\Product;

use App\Models\AuctionProductBid;
use App\Models\Brand;
use App\Models\Cart;
use App\Models\Category;
use App\Models\FlashDealProduct;
use App\Models\FrequentlyBoughtProduct;
use App\Models\LastViewedProduct;
use App\Models\Note;
use App\Models\OrderDetail;
use App\Models\ProductCategory;
use App\Models\ProductQuery;
use App\Models\ProductStock;
use App\Models\ProductTax;
use App\Models\ProductTranslation;
use App\Models\Review;
use App\Models\User;
use App\Models\Warranty;
use App\Models\Wishlist;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait ProductRelationships
{
    /**
     * Get all of the translations for the Product.
     *
     * @return HasMany<ProductTranslation>
     */
    public function product_translations(): HasMany
    {
        return $this->hasMany(ProductTranslation::class);
    }

    /**
     * The categories that belong to the Product.
     *
     * @return BelongsToMany<Category>
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'product_categories');
    }

    /**
     * Get all of the frequently bought products for the Product.
     *
     * @return HasMany<FrequentlyBoughtProduct>
     */
    public function frequently_bought_products(): HasMany
    {
        return $this->hasMany(FrequentlyBoughtProduct::class);
    }

    /**
     * Get all of the product categories for the Product.
     *
     * @return HasMany<ProductCategory>
     */
    public function product_categories(): HasMany
    {
        return $this->hasMany(ProductCategory::class);
    }

    /**
     * Get the brand that owns the Product.
     *
     * @return BelongsTo<Brand, \App\Models\Product>
     */
    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    /**
     * Get the user that owns the Product.
     *
     * @return BelongsTo<User, \App\Models\Product>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all of the order details for the Product.
     *
     * @return HasMany<OrderDetail>
     */
    public function orderDetails(): HasMany
    {
        return $this->hasMany(OrderDetail::class);
    }

    /**
     * Get all of the reviews for the Product.
     *
     * @return HasMany<Review>
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Get all of the product queries for the Product.
     *
     * @return HasMany<ProductQuery>
     */
    public function product_queries(): HasMany
    {
        return $this->hasMany(ProductQuery::class);
    }

    /**
     * Get all of the wishlists for the Product.
     *
     * @return HasMany<Wishlist>
     */
    public function wishlists(): HasMany
    {
        return $this->hasMany(Wishlist::class);
    }

    /**
     * Get all of the stocks for the Product.
     *
     * @return HasMany<ProductStock>
     */
    public function stocks(): HasMany
    {
        return $this->hasMany(ProductStock::class);
    }

    /**
     * Get all of the taxes for the Product.
     *
     * @return HasMany<ProductTax>
     */
    public function taxes(): HasMany
    {
        return $this->hasMany(ProductTax::class);
    }

    /**
     * Get all of the flash deal products for the Product.
     *
     * @return HasMany<FlashDealProduct>
     */
    public function flash_deal_products(): HasMany
    {
        return $this->hasMany(FlashDealProduct::class);
    }

    /**
     * Get all of the bids for the Product.
     *
     * @return HasMany<AuctionProductBid>
     */
    public function bids(): HasMany
    {
        return $this->hasMany(AuctionProductBid::class);
    }

    /**
     * Get all of the carts for the Product.
     *
     * @return HasMany<Cart>
     */
    public function carts(): HasMany
    {
        return $this->hasMany(Cart::class);
    }

    /**
     * Get all of the last viewed products for the Product.
     *
     * @return HasMany<LastViewedProduct>
     */
    public function last_viewed_products(): HasMany
    {
        return $this->hasMany(LastViewedProduct::class);
    }

    /**
     * Get the warranty associated with the Product.
     *
     * @return BelongsTo<Warranty, \App\Models\Product>
     */
    public function warranty(): BelongsTo
    {
        return $this->belongsTo(Warranty::class);
    }

    /**
     * Get the warranty note associated with the Product.
     *
     * @return BelongsTo<Note, \App\Models\Product>
     */
    public function warrantyNote(): BelongsTo
    {
        return $this->belongsTo(Note::class, 'warranty_note_id');
    }

    /**
     * Get the refund note associated with the Product.
     *
     * @return BelongsTo<Note, \App\Models\Product>
     */
    public function refundNote(): BelongsTo
    {
        return $this->belongsTo(Note::class, 'refund_note_id');
    }
}
