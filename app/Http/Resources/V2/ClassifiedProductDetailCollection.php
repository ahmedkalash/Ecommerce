<?php

namespace App\Http\Resources\V2;

use App\Models\Attribute;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ClassifiedProductDetailCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return [
            'data' => $this->collection->map(function ($data) {

                $photos = [];
                foreach ($data->galleryMedia() as $mediaItem) {
                    $photos[] = [
                        'variant' => '',
                        'path' => $mediaItem->getUrl(),
                    ];
                }

                $brand = [
                    'id' => 0,

                    'slug' => '',

                    'name' => '',
                    'logo' => '',
                ];

                if ($data->brand != null) {
                    $brand = [
                        'id' => $data->brand->id,
                        'slug' => $data->brand->slug,
                        'name' => $data->brand->getTranslation('name'),
                        'logo' => get_file_by_id($data->brand->logo),
                    ];
                }

                return [
                    'id' => (int) $data->id,
                    'name' => $data->getTranslation('name'),
                    'added_by' => $data->user->name,
                    'phone' => $data->user->phone ?? '',
                    'condition' => $data->conditon,
                    'photos' => new UploadedFileCollection($data->getMedia('gallery')),
                    'thumbnail_image' => new UploadedFileCollection($data->getMedia('thumbnail')),
                    'tags' => explode(',', $data->tags),
                    'location' => $data->location,
                    'unit_price' => single_price($data->unit_price),
                    'unit' => $data->unit ?? '',
                    'description' => $data->getTranslation('description'),
                    'video_link' => $data->video_link != null ? $data->video_link : '',
                    'brand' => $brand,
                    'category' => $data->category->getTranslation('name'),
                    'link' => route('customer.product', $data->slug),
                    'meta_title' => $data->meta_title,
                    'meta_description' => $data->meta_description,
                    'meta_image' => new UploadedFileCollection($data->getMedia('meta')),
                    'pdf' => new UploadedFileCollection($data->getMedia('pdf')),
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

    protected function convertToChoiceOptions($data)
    {
        $result = [];
        if ($data) {
            foreach ($data as $key => $choice) {
                $item['name'] = $choice->attribute_id;
                $item['title'] = Attribute::find($choice->attribute_id)->getTranslation('name');
                $item['options'] = $choice->values;
                array_push($result, $item);
            }
        }

        return $result;
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
