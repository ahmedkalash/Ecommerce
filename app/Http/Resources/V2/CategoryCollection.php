<?php

namespace App\Http\Resources\V2;

use App\Utility\CategoryUtility;
use Illuminate\Http\Resources\Json\ResourceCollection;

class CategoryCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return [
            'data' => $this->collection->map(function ($data) {
                $banner = '';
                if (get_file_by_id($data->banner)) {
                    $banner = get_file_by_id($data->banner);
                }
                $icon = '';
                if (get_file_by_id(get_file_by_id($data->icon))) {
                    $icon = get_file_by_id($data->icon);
                }

                return [
                    'id' => $data->id,
                    'slug' => $data->slug,
                    'name' => $data->getTranslation('name'),
                    'cover_image' => get_file_by_id($data->cover_image),
                    'banner' => $banner,
                    'icon' => $icon,
                    'number_of_children' => CategoryUtility::get_immediate_children_count($data->id),
                    'links' => [
                        'products' => route('api.products.category', $data->id),
                        'sub_categories' => route('subCategories.index', $data->id),
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
