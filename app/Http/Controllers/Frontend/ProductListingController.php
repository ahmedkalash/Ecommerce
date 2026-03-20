<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProductListingController extends Controller
{
    public function index(Request $request, $slug = null)
    {
        $category = null;
        $brand = null;

        if ($request->route()->getName() === 'products.category') {
            $category = $slug;
        } elseif ($request->route()->getName() === 'products.brand') {
            $brand = $slug;
        }

        return view('frontend.catalog.index', compact('category', 'brand'));
    }
}
