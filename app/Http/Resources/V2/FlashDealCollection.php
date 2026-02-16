<?php

namespace App\Http\Resources\V2;

use Illuminate\Http\Resources\Json\ResourceCollection;

class FlashDealCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'data' => $this->collection->map(function ($data) {
                return [
                    'id' => $data->id,
                    'slug' => $data->slug,
                    'title' => $data->title,
                    'date' => (int) $data->end_date,
                    'banner' => get_file_by_id($data->banner),
                    'products' => new FlashDealProductCollection($data->flash_deal_products()->take(6)->get()),
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
