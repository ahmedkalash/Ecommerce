<?php

namespace App\DTOs;

use App\Enums\SpecialPriceType;
use Carbon\Carbon;

readonly class ProductStockData
{
    /**
     * @param  string  $variant  Variant name/combination (e.g., "Red-XL")
     * @param  float  $price  Unit price for this specific variant
     * @param  int  $qty  Current stock quantity
     * @param  string|null  $sku  Unique Stock Keeping Unit
     * @param  int  $min_qty  Minimum order quantity
     * @param  bool  $cash_on_delivery  Whether Cash-on-Delivery is supported
     * @param  bool  $todays_deal  Whether this variant is part of Today's Deal
     * @param  float|null  $special_price  Discounted value or fixed price
     * @param  SpecialPriceType|null  $special_price_type  The strategy for special pricing
     * @param  \Carbon\Carbon|null  $special_price_start  Start date of special price
     * @param  \Carbon\Carbon|null  $special_price_end  End date of special price
     * @param  array<string, mixed>  $extra_attributes  Miscellaneous metadata
     */
    public function __construct(
        public string $variant,
        public float $price,
        public int $qty = 0,
        public ?string $sku = null,
        public int $min_qty = 1,
        public bool $cash_on_delivery = true,
        public bool $todays_deal = false,
        public ?float $special_price = null,
        public ?SpecialPriceType $special_price_type = null,
        public ?Carbon $special_price_start = null,
        public ?Carbon $special_price_end = null,
        public array $extra_attributes = [],
    ) {}

    /**
     * Create a ProductStockData instance from a raw associative array.
     *
     * Handles type-casting and parsing for all fields, including
     * special price dates (timestamps or date strings) and enum values.
     *
     * @param  array{
     *     variant?: string,
     *     price?: float|string,
     *     qty?: int|string,
     *     sku?: string|null,
     *     min_qty?: int|string,
     *     cash_on_delivery?: bool|string,
     *     todays_deal?: bool|string,
     *     special_price?: float|string|null,
     *     special_price_type?: string|SpecialPriceType|null,
     *     special_price_start?: string|int|\Carbon\Carbon|null,
     *     special_price_end?: string|int|\Carbon\Carbon|null,
     *     extra_attributes?: array<string, mixed>|string,
     * }  $data  Raw input data (e.g., from request or legacy import)
     */
    public static function fromArray(array $data): self
    {
        return new self(
            variant: (string) ($data['variant'] ?? 'Default'),
            price: (float) ($data['price'] ?? 0),
            qty: (int) ($data['qty'] ?? 0),
            sku: $data['sku'] ?? null,
            min_qty: (int) ($data['min_qty'] ?? 1),
            cash_on_delivery: filter_var($data['cash_on_delivery'] ?? true, FILTER_VALIDATE_BOOLEAN),
            todays_deal: filter_var($data['todays_deal'] ?? false, FILTER_VALIDATE_BOOLEAN),
            special_price: self::parseSpecialPrice($data['special_price'] ?? null),
            special_price_type: self::parseSpecialPriceType($data['special_price_type'] ?? null),
            special_price_start: self::parseDate($data['special_price_start'] ?? null),
            special_price_end: self::parseDate($data['special_price_end'] ?? null),
            extra_attributes: self::parseExtraAttributes($data['extra_attributes'] ?? []),
        );
    }

    private static function parseSpecialPrice(mixed $value): ?float
    {
        if ($value === null) {
            return null;
        }

        $val = (float) $value;

        return $val > 0 ? $val : null;
    }

    private static function parseSpecialPriceType(mixed $value): ?SpecialPriceType
    {
        if ($value instanceof SpecialPriceType) {
            return $value;
        }

        if (is_string($value)) {
            return SpecialPriceType::tryFrom($value);
        }

        return null;
    }

    private static function parseDate(mixed $date): ?Carbon
    {
        if (! $date) {
            return null;
        }

        if ($date instanceof Carbon) {
            return $date;
        }

        try {
            if (is_numeric($date)) {
                return Carbon::createFromTimestamp((int) $date);
            }

            return Carbon::parse((string) $date);
        } catch (\Exception) {
            return null;
        }
    }

    private static function parseExtraAttributes(mixed $extra): array
    {
        if (is_string($extra)) {
            return json_decode($extra, true) ?? [];
        }

        return is_array($extra) ? $extra : [];
    }
}
