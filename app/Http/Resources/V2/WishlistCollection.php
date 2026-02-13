<?php

namespace App\Http\Resources\V2;

use Illuminate\Http\Resources\Json\ResourceCollection;

class WishlistCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return [
            'data' => $this->collection->map(function ($data) {
                return [
                    'id' => (int) $data->id,
                    'product' => [
                        'id' => $data->product->id,
                        'name' => $data->product->name,
                        'slug' => $data->product->slug,
                        'thumbnail_image' => get_file_by_id($data->product->thumbnail_img),
                        'base_price' => format_price(home_base_price($data->product, false)),
                        'rating' => (float) $data->product->rating,
                    ],
                ];
            }),
        ];
    }

    public function with($request)
    {
        return [
            'success' => true,
            'status' => 200,
        ];
    }
}
