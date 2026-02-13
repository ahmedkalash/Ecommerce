<?php

namespace App\Http\Resources\V2;

use App\Models\Review;
use Illuminate\Http\Resources\Json\ResourceCollection;

class DigitalProductDetailCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return [
            'data' => $this->collection->map(function ($data) {
                $precision = 2;
                $calculable_price = home_discounted_base_price($data, false);
                $calculable_price = number_format($calculable_price, $precision, '.', '');
                $calculable_price = floatval($calculable_price);
                $photos = [];

                foreach ($data->galleryMedia() as $mediaItem) {
                    $photos[] = [
                        'variant' => '',
                        'path' => $mediaItem->getUrl(),
                    ];
                }

                return [
                    'id' => (int) $data->id,
                    'name' => $data->getTranslation('name'),
                    'added_by' => $data->added_by,
                    'seller_id' => $data->user->id,
                    'shop_id' => $data->added_by == 'admin' ? 0 : $data->user->shop->id,
                    'shop_name' => $data->added_by == 'admin' ? translate('In House Product') : $data->user->shop->name,
                    'shop_logo' => $data->added_by == 'admin' ? get_file_by_id(get_setting('header_logo')) : get_file_by_id($data->user->shop->logo) ?? '',
                    'photos' => $photos,
                    'thumbnail_image' => $data->thumbnail_img,
                    'tags' => explode(',', $data->tags),
                    'price_high_low' => (float) explode('-',
                        home_discounted_base_price($data, false))[0] == (float) explode('-',
                            home_discounted_price($data, false))[1] ? format_price((float) explode('-',
                                home_discounted_price($data, false))[0]) : 'From '.format_price((float) explode('-',
                                    home_discounted_price($data, false))[0]).' to '.format_price((float) explode('-',
                                        home_discounted_price($data, false))[1]),
                    'has_discount' => home_base_price($data, false) != home_discounted_base_price($data, false),
                    'stroked_price' => home_base_price($data),
                    'main_price' => home_discounted_base_price($data),
                    'calculable_price' => $calculable_price,
                    'currency_symbol' => currency_symbol(),
                    'rating' => (float) $data->rating,
                    'rating_count' => (int) Review::where(['product_id' => $data->id])->count(),
                    'earn_point' => (float) $data->earn_point,
                    'description' => $data->getTranslation('description'),
                    'video_link' => $data->video_link != null ? $data->video_link : '',
                    'link' => route('product', $data->slug),
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
