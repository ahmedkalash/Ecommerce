<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class DigitalProductController extends Controller
{
    public function download(Request $request)
    {
        $product = Product::findOrFail($request->id);
        $orders = Order::select('id')->where('user_id', auth()->user()->id)->pluck('id');
        $orderDetails = OrderDetail::where('product_id', $request->id)->whereIn('order_id', $orders)->get();
        if (auth()->user()->user_type == 'admin' || auth()->user()->id == $product->user_id || $orderDetails) {
            $upload = Media::findOrFail($product->file_name);

            return response()->download($upload->getPath(),
                config('app.name').'_'.$upload->name.'.'.$upload->extension);
        } else {
            return response()->download(file('dd.pdf'), 'failed.jpg');
        }
    }
}
