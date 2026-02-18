<?php

namespace App\DataTransferObjects;

use App\Enums\ShippingType;

readonly class ProductData
{
    /**
     * @param  int[]  $category_ids
     * @param  string[]  $tags
     * @param  array<string, mixed>  $extra_attributes
     * @param  ProductStockData[]  $stocks
     */
    public function __construct(
        public string $name,
        public ?int $brand_id = null,
        public ?string $description = null,
        public string $added_by = 'admin',
        public ?int $user_id = null,
        public bool $published = true,
        public bool $approved = true,
        public ShippingType $shipping_type = ShippingType::FREE_SHIPPING,
        public float $shipping_cost = 0,
        public ?int $est_shipping_days = null,
        public ?string $meta_title = null,
        public ?string $meta_description = null,
        public ?string $slug = null,
        public bool $digital = false,
        public bool $wholesale_product = false,
        public bool $has_warranty = false,
        public array $category_ids = [],
        public array $tags = [],
        public array $stocks = [],
        public array $extra_attributes = [],
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        $stocks = $data['stocks'] ?? [];

        // Support for legacy "simple" form data (no 'stocks' array provided)
        if (empty($stocks) && (isset($data['unit_price']) || isset($data['sku']))) {
            $stocks = [
                [
                    'variant' => 'Default',
                    'price' => $data['unit_price'] ?? 0,
                    'qty' => $data['current_stock'] ?? 0,
                    'sku' => $data['sku'] ?? null,
                    'min_qty' => $data['min_qty'] ?? 1,
                    'cash_on_delivery' => $data['cash_on_delivery'] ?? true,
                    'todays_deal' => $data['todays_deal'] ?? false,
                    // Legacy special price mapping
                    'special_price' => $data['discount'] ?? null,
                    'special_price_type' => ($data['discount_type'] ?? 'amount') === 'percent'
                        ? 'discount_percent'
                        : null, // Note: Absolute discount needs to be handled separately or converted
                ],
            ];

            // If it was absolute discount in legacy, we have to convert it to FixedPrice for the new DTO
            if (($data['discount_type'] ?? 'amount') === 'amount' && isset($data['discount']) && $data['discount'] > 0) {
                $stocks[0]['special_price'] = ($data['unit_price'] ?? 0) - $data['discount'];
                $stocks[0]['special_price_type'] = 'fixed_price';
            }
        }

        return new self(
            name: (string) ($data['name'] ?? ''),
            brand_id: isset($data['brand_id']) ? (int) $data['brand_id'] : null,
            description: $data['description'] ?? null,
            added_by: $data['added_by'] ?? 'admin',
            user_id: isset($data['user_id']) ? (int) $data['user_id'] : null,
            published: filter_var($data['published'] ?? true, FILTER_VALIDATE_BOOLEAN),
            approved: filter_var($data['approved'] ?? true, FILTER_VALIDATE_BOOLEAN),
            shipping_type: self::parseShippingType($data['shipping_type'] ?? null),
            shipping_cost: (float) ($data['shipping_cost'] ?? 0),
            est_shipping_days: isset($data['est_shipping_days']) ? (int) $data['est_shipping_days'] : null,
            meta_title: $data['meta_title'] ?? null,
            meta_description: $data['meta_description'] ?? null,
            slug: $data['slug'] ?? null,
            digital: filter_var($data['digital'] ?? false, FILTER_VALIDATE_BOOLEAN),
            wholesale_product: filter_var($data['wholesale_product'] ?? false, FILTER_VALIDATE_BOOLEAN),
            has_warranty: filter_var($data['has_warranty'] ?? false, FILTER_VALIDATE_BOOLEAN),
            category_ids: (array) ($data['categories'] ?? $data['category_ids'] ?? []),
            tags: (array) ($data['tags'] ?? []),
            stocks: self::parseStocks($stocks),
            extra_attributes: (array) ($data['extra_attributes'] ?? []),
        );
    }

    private static function parseShippingType(mixed $value): ShippingType
    {
        if ($value instanceof ShippingType) {
            return $value;
        }

        return ShippingType::tryFrom((string) $value) ?? ShippingType::FREE_SHIPPING;
    }

    /**
     * @return ProductStockData[]
     */
    private static function parseStocks(array $stocks): array
    {
        return array_map(fn ($item) => ProductStockData::fromArray($item), $stocks);
    }
}
