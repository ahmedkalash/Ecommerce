<?php

namespace App\Http\Resources\V2;

use Illuminate\Http\Resources\Json\ResourceCollection;

class HomeCategoryCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return [
            'data' => $this->collection->map(function ($data) {
                return [
                    'name' => $data->category->getTranslation('name'),
                    'banner' => get_file_by_id($data->category->banner),
                    'icon' => get_file_by_id($data->category->icon),
                    'links' => [
                        'products' => route('api.products.category', $data->category->id),
                        'sub_categories' => route('subCategories.index', $data->category->id),
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
