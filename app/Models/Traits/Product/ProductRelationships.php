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
use Illuminate\Database\Eloquent\Relations\HasMany;

trait ProductRelationships
{
    public function product_translations()
    {
        return $this->hasMany(ProductTranslation::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'product_categories');
    }

    public function frequently_bought_products()
    {
        return $this->hasMany(FrequentlyBoughtProduct::class);
    }

    public function product_categories()
    {
        return $this->hasMany(ProductCategory::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    /**
     * @return BelongsTo<User>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function product_queries()
    {
        return $this->hasMany(ProductQuery::class);
    }

    /**
     * @return HasMany<Wishlist>
     */
    public function wishlists(): HasMany
    {
        return $this->hasMany(Wishlist::class);
    }

    /**
     * @return HasMany<ProductStock>
     */
    public function stocks(): HasMany
    {
        return $this->hasMany(ProductStock::class);
    }

    public function taxes()
    {
        return $this->hasMany(ProductTax::class);
    }

    public function flash_deal_products()
    {
        return $this->hasMany(FlashDealProduct::class);
    }

    public function bids()
    {
        return $this->hasMany(AuctionProductBid::class);
    }

    public function carts()
    {
        return $this->hasMany(Cart::class);
    }

    public function last_viewed_products()
    {
        return $this->hasMany(LastViewedProduct::class);
    }

    public function warranty()
    {
        return $this->belongsTo(Warranty::class);
    }

    public function warrantyNote()
    {
        return $this->belongsTo(Note::class, 'warranty_note_id');
    }

    public function refundNote()
    {
        return $this->belongsTo(Note::class, 'refund_note_id');
    }
}
