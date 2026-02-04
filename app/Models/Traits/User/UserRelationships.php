<?php

namespace App\Models\Traits\User;

use App\Models\Address;
use App\Models\AffiliateLog;
use App\Models\AffiliateUser;
use App\Models\AffiliateWithdrawRequest;
use App\Models\AuctionProductBid;
use App\Models\Cart;
use App\Models\ClubPoint;
use App\Models\Customer;
use App\Models\CustomerPackage;
use App\Models\CustomerPackagePayment;
use App\Models\CustomerProduct;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Preorder;
use App\Models\PreorderProduct;
use App\Models\Product;
use App\Models\ProductQuery;
use App\Models\Review;
use App\Models\Seller;
use App\Models\SellerPackagePayment;
use App\Models\Shop;
use App\Models\Staff;
use App\Models\Upload;
use App\Models\UserCoupon;
use App\Models\Wallet;
use App\Models\Wishlist;

trait UserRelationships
{
    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }

    public function customer()
    {
        return $this->hasOne(Customer::class);
    }

    public function affiliate_user()
    {
        return $this->hasOne(AffiliateUser::class);
    }

    public function affiliate_withdraw_request()
    {
        return $this->hasMany(AffiliateWithdrawRequest::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function shop()
    {
        return $this->hasOne(Shop::class);
    }

    public function seller()
    {
        return $this->hasOne(Seller::class);
    }

    public function staff()
    {
        return $this->hasOne(Staff::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function seller_orders()
    {
        return $this->hasMany(Order::class, 'seller_id');
    }

    public function seller_sales()
    {
        return $this->hasMany(OrderDetail::class, 'seller_id');
    }

    public function wallets()
    {
        return $this->hasMany(Wallet::class)->orderBy('created_at', 'desc');
    }

    public function club_point()
    {
        return $this->hasOne(ClubPoint::class);
    }

    public function customer_package()
    {
        return $this->belongsTo(CustomerPackage::class);
    }

    public function customer_package_payments()
    {
        return $this->hasMany(CustomerPackagePayment::class);
    }

    public function customer_products()
    {
        return $this->hasMany(CustomerProduct::class);
    }

    public function seller_package_payments()
    {
        return $this->hasMany(SellerPackagePayment::class);
    }

    public function carts()
    {
        return $this->hasMany(Cart::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    public function affiliate_log()
    {
        return $this->hasMany(AffiliateLog::class);
    }

    public function product_bids()
    {
        return $this->hasMany(AuctionProductBid::class);
    }

    public function product_queries()
    {
        return $this->hasMany(ProductQuery::class, 'customer_id');
    }

    public function uploads()
    {
        return $this->hasMany(Upload::class);
    }

    public function userCoupon()
    {
        return $this->hasOne(UserCoupon::class);
    }

    public function preorderProducts()
    {
        return $this->hasMany(PreorderProduct::class);
    }

    public function preorders()
    {
        return $this->hasMany(Preorder::class);
    }
}
