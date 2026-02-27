<?php

namespace App\DTOs;

use App\Enums\Coupons\CouponDiscountTypes;
use App\Enums\Coupons\CouponTypes;
use Carbon\Carbon;

readonly class CouponDTO
{
    public function __construct(
        public CouponTypes $type,
        public string $label,
        public string $code,
        public float $discount,
        public CouponDiscountTypes $discount_type,
        public ?float $min_money_spent,
        public ?float $max_discount,
        public ?int $usage_limit,
        public array $product_ids,
        public Carbon $start_date,
        public Carbon $end_date,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            type: CouponTypes::from($data['type']),
            label: $data['label'],
            code: $data['code'],
            discount: (float) $data['discount'],
            discount_type: CouponDiscountTypes::from($data['discount_type']),
            min_money_spent: isset($data['min_money_spent']) ? (float) $data['min_money_spent'] : null,
            max_discount: isset($data['max_discount']) ? (float) $data['max_discount'] : null,
            usage_limit: isset($data['usage_limit']) ? (int) $data['usage_limit'] : null,
            product_ids: isset($data['product_ids']) ? (array) $data['product_ids'] : [],
            start_date: Carbon::parse($data['start_date']),
            end_date: Carbon::parse($data['end_date']),
        );
    }

    public function toArray(): array
    {
        return [
            'type' => $this->type->value,
            'label' => $this->label,
            'code' => $this->code,
            'discount' => $this->discount,
            'discount_type' => $this->discount_type->value,
            'min_money_spent' => $this->min_money_spent,
            'max_discount' => $this->max_discount,
            'usage_limit' => $this->usage_limit,
            'product_ids' => $this->product_ids,
            'start_date' => $this->start_date->toDateTimeString(),
            'end_date' => $this->end_date->toDateTimeString(),
        ];
    }
}
