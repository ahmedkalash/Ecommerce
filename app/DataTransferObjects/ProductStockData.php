<?php

namespace App\DataTransferObjects;

use App\Enums\SpecialPriceType;
use Carbon\Carbon;

readonly class ProductStockData
{
    /**
     * @param  array<string, mixed>  $extra_attributes
     * @param  WholesalePriceData[]  $wholesale_prices
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
        public array $wholesale_prices = [],
        public array $extra_attributes = [],
    ) {}

    /**
     * @param  array<string, mixed>  $data
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
            wholesale_prices: self::parseWholesalePrices($data['wholesale_prices'] ?? []),
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

    /**
     * @return WholesalePriceData[]
     */
    private static function parseWholesalePrices(iterable $prices): array
    {
        $data = [];
        foreach ($prices as $price) {
            $data[] = WholesalePriceData::fromArray((array) $price);
        }

        return $data;
    }

    private static function parseExtraAttributes(mixed $extra): array
    {
        if (is_string($extra)) {
            return json_decode($extra, true) ?? [];
        }

        return is_array($extra) ? $extra : [];
    }
}
