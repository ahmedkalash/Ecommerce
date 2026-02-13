<?php

namespace App\Http\Resources\V2;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ShopCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return [
            'data' => $this->collection->map(function ($data) {
                return [
                    'id' => $data->id,
                    'slug' => $data->slug,
                    'name' => $data->name,
                    'logo' => get_file_by_id($data->logo),
                    'rating' => $data->rating,
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

    protected function convertPhotos($data)
    {
        $result = [];
        foreach ($data as $key => $item) {
            array_push($result, get_file_by_id($item));
        }

        return $result;
    }
}
