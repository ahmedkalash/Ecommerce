<?php

namespace App\Http\Controllers\Seller;

use AizPackages\CombinationGenerate\Services\CombinationService;
use App\DTOs\ProductData;
use App\Http\Requests\ProductRequest;
use App\Models\AttributeValue;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductTranslation;
use App\Models\User;
use App\Notifications\ShopProductNotification;
use App\Services\FrequentlyBoughtProductService;
use App\Services\MediaService;
use App\Services\ProductFlashDealService;
use App\Services\ProductService;
use App\Services\ProductStockService;
use App\Services\ProductTaxService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;

class ProductController extends Controller
{
    protected $productService;

    protected $productCategoryService;

    protected $productTaxService;

    protected $productFlashDealService;

    protected $productStockService;

    protected $frequentlyBoughtProductService;

    protected $mediaService;

    public function __construct(
        ProductService $productService,
        ProductTaxService $productTaxService,
        ProductFlashDealService $productFlashDealService,
        ProductStockService $productStockService,
        FrequentlyBoughtProductService $frequentlyBoughtProductService,
        MediaService $mediaService
    ) {
        $this->productService = $productService;
        $this->productTaxService = $productTaxService;
        $this->productFlashDealService = $productFlashDealService;
        $this->productStockService = $productStockService;
        $this->frequentlyBoughtProductService = $frequentlyBoughtProductService;
        $this->mediaService = $mediaService;
    }

    public function index(Request $request)
    {
        $search = null;
        $products = Product::where('user_id', Auth::user()->id)->where('digital', 0)->where('auction_product', 0)->where('wholesale_product', 0)->orderBy('created_at', 'desc');
        if ($request->has('search')) {
            $search = $request->search;
            $products = $products->where('name', 'like', '%'.$search.'%');
        }
        $products = $products->paginate(10);

        return view('seller.product.products.index', compact('products', 'search'));
    }

    public function create(Request $request)
    {
        if (addon_is_activated('seller_subscription')) {
            if (! seller_package_validity_check()) {
                flash(translate('Please upgrade your package.'))->warning();

                return back();
            }
        }
        $categories = Category::whereNull('parent_id')
            ->where('digital', 0)
            ->with('childrenCategories')
            ->get();

        return view('seller.product.products.create', compact('categories'));
    }

    public function store(ProductRequest $request)
    {
        // Create Product via Service with DTO
        $product = $this->productService->store(ProductData::fromArray($request->all()));

        $request->merge(['product_id' => $product->id]);

        // VAT & Tax
        if ($request->tax_id) {
            $this->productTaxService->store($request->only([
                'tax_id',
                'tax',
                'tax_type',
                'product_id',
            ]));
        }

        // Frequently Bought Products
        $this->frequentlyBoughtProductService->store($request->only([
            'product_id',
            'frequently_bought_selection_type',
            'fq_bought_product_ids',
            'fq_bought_product_category_id',
        ]));

        // Product Translations
        $request->merge(['lang' => env('DEFAULT_LANGUAGE')]);
        ProductTranslation::create($request->only([
            'lang',
            'name',
            'description',
            'product_id',
        ]));

        if (get_setting('product_approve_by_admin') == 1) {
            $users = User::findMany(User::where('user_type', 'admin')->first()->id);

            $data = [];
            $data['product_type'] = 'physical';
            $data['status'] = 'pending';
            $data['product'] = $product;
            $data['notification_type_id'] = get_notification_type('seller_product_upload', 'type')->id;

            Notification::send($users, new ShopProductNotification($data));
        }

        flash(translate('Product has been inserted successfully'))->success();

        Artisan::call('view:clear');
        Artisan::call('cache:clear');

        return redirect()->route('seller.products');
    }

    public function edit(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        if (Auth::user()->id != $product->user_id) {
            flash(translate('This product is not yours.'))->warning();

            return back();
        }

        $lang = $request->lang;
        $tags = json_decode($product->tags);
        $categories = Category::whereNull('parent_id')
            ->where('digital', 0)
            ->with('childrenCategories')
            ->get();

        return view('seller.product.products.edit', compact('product', 'categories', 'tags', 'lang'));
    }

    public function update(ProductRequest $request, Product $product)
    {
        // Update Product via Service with DTO
        $product = $this->productService->update(ProductData::fromArray($request->all()), $product);

        $request->merge(['product_id' => $product->id]);

        // VAT & Tax
        if ($request->tax_id) {
            $product->taxes()->delete();
            $request->merge(['product_id' => $product->id]);
            $this->productTaxService->store($request->only([
                'tax_id',
                'tax',
                'tax_type',
                'product_id',
            ]));
        }

        // Frequently Bought Products
        $product->frequently_bought_products()->delete();
        $this->frequentlyBoughtProductService->store($request->only([
            'product_id',
            'frequently_bought_selection_type',
            'fq_bought_product_ids',
            'fq_bought_product_category_id',
        ]));

        // Product Translations
        ProductTranslation::updateOrCreate(
            $request->only([
                'lang',
                'product_id',
            ]),
            $request->only([
                'name',
                'description',
            ])
        );

        flash(translate('Product has been updated successfully'))->success();

        Artisan::call('view:clear');
        Artisan::call('cache:clear');

        return back();
    }

    // ... Copy over other methods if needed (sku_combination, etc.)
    public function sku_combination(Request $request)
    {
        $options = [];
        if ($request->has('colors_active') && $request->has('colors') && count($request->colors) > 0) {
            $colors_active = 1;
            array_push($options, $request->colors);
        } else {
            $colors_active = 0;
        }

        $unit_price = $request->unit_price;
        $product_name = $request->name;

        if ($request->has('choice_no')) {
            foreach ($request->choice_no as $key => $no) {
                $name = 'choice_options_'.$no;
                $data = [];
                foreach ($request[$name] as $key => $item) {
                    array_push($data, $item);
                }
                array_push($options, $data);
            }
        }

        $combinations = (new CombinationService)->generate_combination($options);

        // Need to update view to probably show/hide video inputs per variant if needed?
        // For now adhering to existing view logic.
        return view('backend.product.products.sku_combinations', compact('combinations', 'unit_price', 'colors_active', 'product_name'));
    }

    public function sku_combination_edit(Request $request)
    {
        $product = Product::findOrFail($request->id);

        $options = [];
        if ($request->has('colors_active') && $request->has('colors') && count($request->colors) > 0) {
            $colors_active = 1;
            array_push($options, $request->colors);
        } else {
            $colors_active = 0;
        }

        $product_name = $request->name;
        $unit_price = $request->unit_price;

        if ($request->has('choice_no')) {
            foreach ($request->choice_no as $key => $no) {
                $name = 'choice_options_'.$no;
                $data = [];
                foreach ($request[$name] as $key => $item) {
                    array_push($data, $item);
                }
                array_push($options, $data);
            }
        }

        $combinations = (new CombinationService)->generate_combination($options);

        return view('backend.product.products.sku_combinations_edit', compact('combinations', 'unit_price', 'colors_active', 'product_name', 'product'));
    }

    public function add_more_choice_option(Request $request)
    {
        $all_attribute_values = AttributeValue::with('attribute')->where('attribute_id', $request->attribute_id)->get();

        $html = '';

        foreach ($all_attribute_values as $row) {
            $html .= '<option value="'.$row->value.'">'.$row->value.'</option>';
        }

        echo json_encode($html);
    }

    public function updatePublished(Request $request)
    {
        $product = Product::findOrFail($request->id);
        $product->published = $request->status;
        if (addon_is_activated('seller_subscription') && $request->status == 1) {
            if (! seller_package_validity_check()) {
                return 2;
            }
        }
        $product->save();

        return 1;
    }

    public function updateFeatured(Request $request)
    {
        $product = Product::findOrFail($request->id);
        $product->seller_featured = $request->status;
        if ($product->save()) {
            Artisan::call('view:clear');
            Artisan::call('cache:clear');

            return 1;
        }

        return 0;
    }

    public function duplicate($id)
    {
        $product = Product::find($id);

        if (Auth::user()->id != $product->user_id) {
            flash(translate('This product is not yours.'))->warning();

            return back();
        }

        if (addon_is_activated('seller_subscription')) {
            if (! seller_package_validity_check()) {
                flash(translate('Please upgrade your package.'))->warning();

                return back();
            }
        }

        // Duplicate Product and its relationships via Service
        $product_new = $this->productService->duplicate($product);

        // VAT & Tax Duplication (Still needed if not in ProductService::duplicate)
        $this->productTaxService->product_duplicate_store($product->taxes, $product_new);

        flash(translate('Product has been duplicated successfully'))->success();

        return redirect()->route('seller.products');
    }

    public function destroy($id)
    {
        $this->productService->destroy($id);

        flash(translate('Product has been deleted successfully'))->success();
        Artisan::call('view:clear');
        Artisan::call('cache:clear');

        return back();
    }

    public function bulk_product_delete(Request $request)
    {
        if ($request->id) {
            foreach ($request->id as $product_id) {
                $this->destroy($product_id);
            }
        }

        return 1;
    }

    public function product_search(Request $request)
    {
        $products = $this->productService->product_search($request->except(['_token']));

        return view('partials.product.product_search', compact('products'));
    }

    public function get_selected_products(Request $request)
    {
        $products = product::whereIn('id', $request->product_ids)->get();

        return view('partials.product.frequently_bought_selected_product', compact('products'));
    }

    public function categoriesWiseProductDiscount(Request $request)
    {
        $sort_search = null;
        $categories = Category::with('sellerDiscount')
            ->orderBy('name', 'asc');
        if ($request->has('search')) {
            $sort_search = $request->search;
            $categories = $categories->where('name', 'like', '%'.$sort_search.'%');
        }
        $categories = $categories->paginate(15);

        return view('seller.product.category_wise_discount.set_discount', compact('categories', 'sort_search'));
    }

    public function setProductDiscount(Request $request)
    {
        $response = $this->productService->setCategoryWiseDiscount($request->except(['_token']));

        return $response;
    }
}
