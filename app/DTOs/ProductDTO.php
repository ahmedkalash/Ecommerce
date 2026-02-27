<?php

namespace App\DTOs;

use App\Enums\ShippingType;
use App\Enums\UserType;
use App\Enums\VideoProvider;
use Illuminate\Support\Str;

readonly class ProductDTO
{
    /**
     * @param  string  $name  Product name
     * @param  string|null  $slug  Unique slug (auto-generated if null)
     * @param  int[]  $categories  List of associated category IDs
     * @param  int|null  $brand_id  Associated brand ID
     * @param  string|null  $unit  Unit of measurement (e.g., "kg", "pcs")
     * @param  float  $min_qty  Minimum purchase quantity
     * @param  string[]|null  $tags  Searchable tags
     * @param  string|null  $description  Full product description
     * @param  string|null  $thumbnail_img  Thumbnail image path
     * @param  string[]|null  $photos  Gallery image paths
     * @param  string|null  $video_provider  Video source (youtube, dailymotion, vimeo)
     * @param  string|null  $video_link  Full URL to the video
     * @param  ProductStockDTO[]  $stocks  Variant definitions and stock levels
     * @param  bool  $published  Display status on the website
     * @param  bool  $approved  Admin approval status
     * @param  bool  $featured  Global featured flag
     * @param  bool  $seller_featured  Seller-specific featured flag
     * @param  bool  $refundable  Refund eligibility
     * @param  int|null  $user_id  Owner user ID
     * @param  string  $added_by  User type who added the product (admin, seller)
     * @param  string  $shipping_type  Shipping method (flat_rate, free, etc.)
     * @param  float  $shipping_cost  Base shipping fee
     * @param  int  $est_shipping_days  Number of days for delivery
     * @param  string|null  $meta_title  SEO optimized title
     * @param  string|null  $meta_description  SEO optimized description
     * @param  bool  $digital  Flags if the product is a digital download
     * @param  bool  $wholesale_product  Flags if wholesale pricing is enabled
     * @param  bool  $has_warranty  Flags if the product comes with a warranty
     * @param  array<string, mixed>  $extra_attributes  Additional schemaless metadata
     */
    public function __construct(
        public string $name,
        public ?string $slug = null,
        public array $categories = [],
        public ?int $brand_id = null,
        public ?string $unit = null,
        public float $min_qty = 1,
        public ?array $tags = null,
        public ?string $description = null,
        public ?string $thumbnail_img = null,
        public ?array $photos = null,
        public ?string $video_provider = null,
        public ?string $video_link = null,
        public array $stocks = [],
        public bool $published = true,
        public bool $approved = true,
        public bool $featured = false,
        public bool $seller_featured = false,
        public bool $refundable = true,
        public ?int $user_id = null,
        public string $added_by = UserType::ADMIN->value,
        public string $shipping_type = ShippingType::FLAT_RATE->value,
        public float $shipping_cost = 0,
        public int $est_shipping_days = 0,
        public ?string $meta_title = null,
        public ?string $meta_description = null,
        public bool $digital = false,
        public bool $wholesale_product = false,
        public bool $has_warranty = false,
        public array $extra_attributes = [],
    ) {}

    /**
     * Create a ProductDTO instance from a raw associative array.
     *
     * Supports both the modern `stocks[]` format and a legacy fallback
     * that maps `unit_price` / `current_stock` into a single "Default" variant.
     *
     * @param  array<string,mixed>  $data  Raw input (e.g., from request or legacy import)
     */
    public static function fromArray(array $data): self
    {
        $stocks = [];
        if (isset($data['stocks'])) {
            foreach ($data['stocks'] as $stock) {
                $stocks[] = ProductStockDTO::fromArray($stock);
            }
        }

        return new self(
            name: $data['name'],
            slug: $data['slug'] ?? Str::slug($data['name']),
            categories: (array) ($data['categories'] ?? []),
            brand_id: $data['brand_id'] ?? null,
            unit: $data['unit'] ?? null,
            min_qty: (float) ($data['min_qty'] ?? 1),
            tags: is_array($data['tags'] ?? null) ? $data['tags'] : null,
            description: $data['description'] ?? null,
            thumbnail_img: $data['thumbnail_img'] ?? null,
            photos: is_array($data['photos'] ?? null) ? $data['photos'] : null,
            video_provider: $data['video_provider'] ?? VideoProvider::YOUTUBE->value,
            video_link: $data['video_link'] ?? null,
            stocks: $stocks,
            published: (bool) ($data['published'] ?? true),
            approved: (bool) ($data['approved'] ?? true),
            featured: (bool) ($data['featured'] ?? false),
            seller_featured: (bool) ($data['seller_featured'] ?? false),
            refundable: (bool) ($data['refundable'] ?? true),
            user_id: isset($data['user_id']) ? (int) $data['user_id'] : null,
            added_by: $data['added_by'] ?? 'admin',
            shipping_type: $data['shipping_type'] ?? ShippingType::FLAT_RATE->value,
            shipping_cost: (float) ($data['shipping_cost'] ?? 0),
            est_shipping_days: (int) ($data['est_shipping_days'] ?? 0),
            meta_title: $data['meta_title'] ?? null,
            meta_description: $data['meta_description'] ?? null,
            digital: (bool) ($data['digital'] ?? false),
            wholesale_product: (bool) ($data['wholesale_product'] ?? false),
            has_warranty: (bool) ($data['has_warranty'] ?? false),
            extra_attributes: (array) ($data['extra_attributes'] ?? []),
        );
    }
}
