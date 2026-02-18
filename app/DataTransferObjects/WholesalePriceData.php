<?php

namespace App\DataTransferObjects;

readonly class WholesalePriceData
{
    public function __construct(
        public int $min_qty,
        public int $max_qty,
        public float $price,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            min_qty: (int) ($data['min_qty'] ?? 1),
            max_qty: (int) ($data['max_qty'] ?? 1),
            price: (float) ($data['price'] ?? 0),
        );
    }
}
