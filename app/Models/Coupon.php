<?php

namespace App\Models;

use App\Enums\Coupons\CouponDiscountTypes;
use App\Enums\Coupons\CouponTypes;
use App\Traits\PreventDemoModeChanges;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class Coupon extends Model
{
    use HasFactory, HasTranslations, PreventDemoModeChanges;

    /**
     * Fields stored as JSON, served per active locale via spatie/laravel-translatable.
     */
    public array $translatable = ['label'];

    protected $fillable = [
        'type',
        'label',
        'code',
        'discount',
        'discount_type',
        'min_money_spent',
        'max_discount',
        'usage_limit',
        'used_count',
        'product_ids',
        'start_date',
        'end_date',
    ];

    protected $casts = [
        'type' => CouponTypes::class,
        'discount_type' => CouponDiscountTypes::class,
        'product_ids' => 'array',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'discount' => 'decimal:2',
        'min_money_spent' => 'decimal:2',
        'max_discount' => 'decimal:2',
    ];

    public function couponUsages(): HasMany
    {
        return $this->hasMany(CouponUsage::class);
    }

    /**
     * Scope a query to only include active coupons.
     */
    public function scopeActive(Builder $query): void
    {
        $now = now();
        $query->where('start_date', '<=', $now)
            ->where('end_date', '>=', $now)
            ->where(function (Builder $query) {
                $query->whereNull('usage_limit')
                    ->orWhereColumn('used_count', '<', 'usage_limit');
            });
    }
}
