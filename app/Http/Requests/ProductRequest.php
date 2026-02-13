<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [];

        $rules['name'] = 'required|max:255';
        $rules['category_ids'] = 'required';
        $rules['category_id'] = ['required', Rule::in($this->category_ids)];
        $rules['unit'] = 'sometimes|required';
        $rules['min_qty'] = 'sometimes|required|numeric';
        $rules['unit_price'] = 'sometimes|required|numeric|gt:0';
        if ($this->get('discount_type') == 'amount') {
            $rules['discount'] = 'sometimes|required|numeric|lt:unit_price';
        } else {
            $rules['discount'] = 'sometimes|required|numeric|lt:100';
        }
        $rules['current_stock'] = 'sometimes|required|numeric';
        $rules['starting_bid'] = 'sometimes|required|numeric|min:1';
        $rules['auction_date_range'] = 'sometimes|required';

        // Media Rules
        $rules['thumbnail_img'] = 'sometimes|nullable|numeric';
        $rules['photos'] = 'sometimes|nullable|string';
        $rules['meta_img'] = 'sometimes|nullable|numeric';
        $rules['pdf'] = 'sometimes|nullable|numeric';
        $rules['short_video'] = 'sometimes|nullable|file|mimetypes:video/mp4,video/webm|max:10240'; // 10MB limit
        $rules['short_video_thumbnail'] = 'sometimes|nullable|file|mimes:jpeg,png,jpg,gif,svg|max:2048'; // 2MB limit

        return $rules;
    }

    /**
     * Get the validation messages of rules that apply to the request.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'name.required' => translate('Product name is required'),
            'category_ids.required' => translate('Product category is required'),
            'category_id.required' => translate('Main Category is required'),
            'category_id.in' => translate('Main Category must be within selected categories'),
            'unit.required' => translate('Product unit is required'),
            'min_qty.required' => translate('Minimum purchase quantity is required'),
            'min_qty.numeric' => translate('Minimum purchase must be numeric'),
            'unit_price.gt' => translate('The unit price must be greater than 0'),
            'unit_price.required' => translate('Unit price is required'),
            'unit_price.numeric' => translate('Unit price must be numeric'),
            'discount.required' => translate('Discount is required'),
            'discount.numeric' => translate('Discount must be numeric'),
            'discount.lt' => translate('Discount should be less than unit price'),
            'current_stock.required' => translate('Current stock is required'),
            'current_stock.numeric' => translate('Current stock must be numeric'),
            'starting_bid.required' => translate('Starting Bid is required'),
            'starting_bid.numeric' => translate('Starting Bid must be numeric'),
            'starting_bid.min' => translate('Minimum Starting Bid is 1'),
            'auction_date_range.required' => translate('Auction Date Range is required'),

            // Media Messages
            'short_video.max' => translate('Short video must not exceed 10MB'),
            'short_video.mimetypes' => translate('Only MP4 and WebM videos are allowed'),
            'short_video_thumbnail.mimes' => translate('Only jpeg, png, jpg, gif, svg images are allowed'),
            'short_video_thumbnail.max' => translate('Short video thumbnail must not exceed 2MB'),
        ];
    }

    /**
     * Get the error messages for the defined validation rules.*
     *
     * @return array
     */
    public function failedValidation(Validator $validator)
    {
        // dd($this->expectsJson());
        if ($this->expectsJson()) {
            throw new HttpResponseException(response()->json([
                'message' => $validator->errors()->all(),
                'result' => false,
            ], 422));
        } else {
            throw (new ValidationException($validator))
                ->errorBag($this->errorBag)
                ->redirectTo($this->getRedirectUrl());
        }
    }
}
