<?php

return [
    'product' => [
        'name_required' => 'Product name is required.',
        'price_numeric' => 'Price must be a valid number.',
        'sku_unique' => 'This SKU is already in use.',
        'slug_unique' => 'This slug is already taken.',
    ],
    'category' => [
        'name_required' => 'Category name is required.',
        'slug_unique' => 'This slug is already taken.',
    ],
    'coupon' => [
        'code_unique' => 'This coupon code already exists.',
    ],
    'role' => [
        'name_unique' => 'A role with this name already exists.',
    ],
];
