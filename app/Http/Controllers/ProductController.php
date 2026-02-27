<?php

namespace App\Http\Controllers;

use AizPackages\CombinationGenerate\Services\CombinationService;
use App\DTOs\ProductDTO;
use App\Enums\UserType;
use App\Http\Requests\ProductRequest;
use App\Models\Cart;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductTranslation;
use App\Models\User;
use App\Models\Wishlist;
use App\Notifications\ShopProductNotification;
use App\Services\FrequentlyBoughtProductService;
use App\Services\ProductFlashDealService;
use App\Services\ProductService;
use App\Services\ProductStockService;
use App\Services\ProductTaxService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Notification;

// use App\Models\AttributeValue;
// use App\Models\Color;

class ProductController extends Controller
{
    public function __construct(
        protected ProductService $productService,
        protected ProductTaxService $productTaxService,
        protected ProductFlashDealService $productFlashDealService,
        protected ProductStockService $productStockService,
        protected FrequentlyBoughtProductService $frequentlyBoughtProductService
    ) {
        // Staff Permission Check
        $this->middleware(['permission:add_new_product'])->only('create');
        $this->middleware(['permission:show_all_products'])->only('all_products');
        $this->middleware(['permission:show_in_house_products'])->only('admin_products');
        $this->middleware(['permission:show_seller_products'])->only('seller_products');
        $this->middleware(['permission:product_edit'])->only('admin_product_edit', 'seller_product_edit');
        $this->middleware(['permission:product_duplicate'])->only('duplicate');
        $this->middleware(['permission:product_delete'])->only('destroy');
        $this->middleware(['permission:set_category_wise_discount'])->only('categoriesWiseProductDiscount');
    }

    /**
     * Display a listing of the resource.
     */
    public function admin_products(Request $request)
    {
        CoreComponentRepository::instantiateShopRepository();

        $type = 'In House';
        $col_name = null;
        $query = null;
        $sort_search = null;

        $products = Product::where('added_by', UserType::ADMIN->value)->where(
            'auction_product',
            0
        )->where('wholesale_product', 0);

        if ($request->type != null) {
            $var = explode(',', $request->type);
            $col_name = $var[0];
            $query = $var[1];
            $products = $products->orderBy($col_name, $query);
            $sort_type = $request->type;
        }
        if ($request->search != null) {
            $sort_search = $request->search;
            $products = $products
                ->where('name', 'like', '%'.$sort_search.'%')
                ->orWhereHas('stocks', function ($q) use ($sort_search) {
                    $q->where('sku', 'like', '%'.$sort_search.'%');
                });
        }

        $products = $products->where('digital', 0)->with(['stocks', 'categories'])->orderBy(
            'created_at',
            'desc'
        )->paginate(15);

        return view('backend.product.products.index', compact('products', 'type', 'col_name', 'query', 'sort_search'));
    }

    /**
     * Display a listing of the resource.
     */
    public function seller_products(Request $request, $product_type)
    {
        $col_name = null;
        $query = null;
        $seller_id = null;
        $sort_search = null;
        $products = Product::where('added_by', UserType::SELLER->value)->where(
            'auction_product',
            0
        )->where('wholesale_product', 0);
        if ($request->has('user_id') && $request->user_id != null) {
            $products = $products->where('user_id', $request->user_id);
            $seller_id = $request->user_id;
        }
        if ($request->search != null) {
            $products = $products
                ->where('name', 'like', '%'.$request->search.'%');
            $sort_search = $request->search;
        }
        if ($request->type != null) {
            $var = explode(',', $request->type);
            $col_name = $var[0];
            $query = $var[1];
            $products = $products->orderBy($col_name, $query);
            $sort_type = $request->type;
        }
        $products = $product_type == 'physical' ? $products->where('digital', 0) : $products->where('digital', 1);
        $products = $products->with(['stocks', 'categories', 'user'])->orderBy('created_at', 'desc')->paginate(15);
        $type = 'Seller';

        if ($product_type == 'digital') {
            return view('backend.product.digital_products.index', compact('products', 'sort_search', 'type'));
        }

        return view(
            'backend.product.products.index',
            compact('products', 'type', 'col_name', 'query', 'seller_id', 'sort_search')
        );
    }

    public function all_products(Request $request)
    {
        $col_name = null;
        $query = null;
        $seller_id = null;
        $sort_search = null;
        $products = Product::where('auction_product', 0)->where('wholesale_product', 0);
        if (get_setting('vendor_system_activation') != 1) {
            $products = $products->where('added_by', UserType::ADMIN->value);
        }
        if ($request->has('user_id') && $request->user_id != null) {
            $products = $products->where('user_id', $request->user_id);
            $seller_id = $request->user_id;
        }
        if ($request->search != null) {
            $sort_search = $request->search;
            $products = $products
                ->where('name', 'like', '%'.$sort_search.'%')
                ->orWhereHas('stocks', function ($q) use ($sort_search) {
                    $q->where('sku', 'like', '%'.$sort_search.'%');
                });
        }
        if ($request->type != null) {
            $var = explode(',', $request->type);
            $col_name = $var[0];
            $query = $var[1];
            $products = $products->orderBy($col_name, $query);
            $sort_type = $request->type;
        }

        $products = $products->with(['stocks', 'categories', 'user'])->orderBy('created_at', 'desc')->paginate(15);
        $type = 'All';

        return view(
            'backend.product.products.index',
            compact('products', 'type', 'col_name', 'query', 'seller_id', 'sort_search')
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::whereNull('parent_id')
            ->where('digital', 0)
            ->with('childrenCategories')
            ->get();

        return view('backend.product.products.create', compact('categories'));
    }

    public function add_more_choice_option(Request $request)
    {
        // $all_attribute_values = AttributeValue::with('attribute')->where('attribute_id', $request->attribute_id)->get();
        // Updated for schemaless attributes (config based)
        // Request sends 'attribute_id' which is now the attribute key (e.g. 'size')

        $key = $request->attribute_id; // Frontend sends 'attribute_id' via existing JS logic
        $options = config("attributes.presets.{$key}.options", []);

        // If options is simple array ['S', 'M'], make it associative for loop
        // If associative ['Small' => 'S'], use keys as value? No, use Value.
        // Wait, standard options structure in config?
        // Step 5883 config generation:
        // 'size' => [ ..., 'options' => ['XS', 'S'...]] (simple array)
        // 'color' => [ ..., 'options' => ['Name' => 'Code']] (assoc array)

        $html = '';

        if ($key === 'color') {
            // For color, usually frontend handles it via specialized color picker if type is color?
            // But if this endpoint is called for color? (unlikely, usually separate logic)
            // If called for color, return names?
            // Actually, standard Active eCommerce uses 'colors' separate input.
            // This method is for "Choice Options" (Select inputs).
            // But if 'color' is added as a choice option?
            // We return names.
        }

        foreach ($options as $option_key => $option_value) {
            // If simple array: key is index, value is value.
            // If assoc array (Color): key is Name, value is Code.

            $val = is_numeric($option_key) ? $option_value : $option_key; // Use Key as Value for assoc (e.g. 'Red')
            // Wait, for colors, we want the Name 'Red' as the value, passing Code?
            // No, standard system stores Name 'Red'.

            // For Size: value is 'S'.

            $html .= '<option value="'.$val.'">'.$val.'</option>';
        }

        echo json_encode($html);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ProductRequest $request)
    {
        try {
            \DB::beginTransaction();

            $product = $this->productService->store(ProductDTO::fromArray($request->all()));
            $request->merge(['product_id' => $product->id]);

            // Product categories
            $product->categories()->attach($request->category_ids);

            // VAT & Tax
            if ($request->tax_id) {
                $this->productTaxService->store($request->only([
                    'tax_id',
                    'tax',
                    'tax_type',
                    'product_id',
                ]));
            }

            // Flash Deal
            $this->productFlashDealService->store($request->only([
                'flash_deal_id',
                'flash_discount',
                'flash_discount_type',
            ]), $product);

            // Product Stock - REMOVED redundant call (handled in ProductService)
            // But ProductService::store calls ProductStockService::store($data).
            // Since we passed $request->all(), it has everything needed.

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

            \DB::commit();

            flash(translate('Product has been inserted successfully'))->success();

            Artisan::call('view:clear');
            Artisan::call('cache:clear');

            return redirect()->route('products.admin');
        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Product Store Failed: '.$e->getMessage().$e->getTraceAsString());
            flash(translate('Something went wrong'))->error();

            return back();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function admin_product_edit(Request $request, $id)
    {
        CoreComponentRepository::initializeCache();

        $product = Product::findOrFail($id);
        if ($product->digital == 1) {
            return redirect('admin/digitalproducts/'.$id.'/edit');
        }

        $lang = $request->lang;
        $tags = json_decode($product->tags);
        $categories = Category::whereNull('parent_id')
            ->where('digital', 0)
            ->with('childrenCategories')
            ->get();

        return view('backend.product.products.edit', compact('product', 'categories', 'tags', 'lang'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function seller_product_edit(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        if ($product->digital == 1) {
            return redirect('digitalproducts/'.$id.'/edit');
        }
        $lang = $request->lang;
        $tags = json_decode($product->tags);
        // $categories = Category::all();
        $categories = Category::whereNull('parent_id')
            ->where('digital', 0)
            ->with('childrenCategories')
            ->get();

        return view('backend.product.products.edit', compact('product', 'categories', 'tags', 'lang'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ProductRequest $request, Product $product)
    {
        try {
            \DB::beginTransaction();

            // Product Stock (Clear old stocks first, Service will recreate them)
            $product->stocks()->delete();

            // Product Update (Pass full request data so Service can handle Stocks)
            // Note: Service expects 'categories' array key, but Request has 'category_ids'.
            // So Service won't sync categories, we do it below.
            $product = $this->productService->update(ProductDTO::fromArray($request->all()), $product);

            $request->merge(['product_id' => $product->id]);

            // Product categories
            $product->categories()->sync($request->category_ids);

            // Flash Deal
            $this->productFlashDealService->store($request->only([
                'flash_deal_id',
                'flash_discount',
                'flash_discount_type',
            ]), $product);

            // VAT & Tax
            if ($request->tax_id) {
                // $product->taxes()->delete(); // Service store logic?
                // ProductTaxService::store might update or create?
                // Legacy code deleted taxes inside if block?
                // Line 409: $product->taxes()->delete();
                // I should keep it.
                $product->taxes()->delete();
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

            \DB::commit();

            flash(translate('Product has been updated successfully'))->success();

            \Artisan::call('view:clear');
            \Artisan::call('cache:clear');

            if ($request->has('tab') && $request->tab != null) {
                return \Redirect::to(\URL::previous().'#'.$request->tab);
            }

            return back();
        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Product Update Failed: '.$e->getMessage().$e->getTraceAsString());
            flash(translate('Something went wrong'))->error();

            return back();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $result = $this->single_product_delete($id);
        if ($result) {
            flash(translate('Product has been deleted successfully'))->success();
        } else {
            flash(translate('Something went wrong'))->error();
        }

        return back();
    }

    public function single_product_delete($id)
    {
        $product = Product::findOrFail($id);

        $product->product_translations()->delete();
        $product->categories()->detach();
        $product->stocks()->delete();
        $product->taxes()->delete();
        $product->frequently_bought_products()->delete();
        $product->last_viewed_products()->delete();
        $product->flash_deal_products()->delete();
        deleteProductReview($product);
        if (Product::destroy($id)) {
            Cart::where('product_id', $id)->delete();
            Wishlist::where('product_id', $id)->delete();
            Artisan::call('view:clear');
            Artisan::call('cache:clear');

            return 1;
        } else {
            return 0;
        }
    }

    public function bulk_product_delete(Request $request)
    {
        if ($request->id) {
            foreach ($request->id as $product_id) {
                $this->single_product_delete($product_id);
            }
        }

        return 1;
    }

    /**
     * Duplicates the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function duplicate(Request $request, $id)
    {
        $product = Product::find($id);

        // Duplicate Product and its relationships via Service
        $product_new = $this->productService->duplicate($product);

        // VAT & Tax Duplication
        $this->productTaxService->product_duplicate_store($product->taxes, $product_new);

        // Frequently Bought Products
        $this->frequentlyBoughtProductService->product_duplicate_store(
            $product->frequently_bought_products,
            $product_new
        );

        flash(translate('Product has been duplicated successfully'))->success();
        if ($request->type == 'In House') {
            return redirect()->route('products.admin');
        } elseif ($request->type == 'Seller') {
            return redirect()->route('products.seller');
        } elseif ($request->type == 'All') {
            return redirect()->route('products.all');
        } elseif ($request->type == 'SellerProfile') {
            return back();
        }
    }

    public function get_products_by_brand(Request $request)
    {
        $products = Product::where('brand_id', $request->brand_id)->get();

        return view('partials.product_select', compact('products'));
    }

    public function updateTodaysDeal(Request $request)
    {
        $product = Product::findOrFail($request->id);
        $product->todays_deal = $request->status;
        $product->save();
        Cache::forget('todays_deal_products');

        return 1;
    }

    public function updatePublished(Request $request)
    {
        $product = Product::findOrFail($request->id);
        $product->published = $request->status;

        if ($product->added_by == UserType::SELLER->value && addon_is_activated('seller_subscription') && $request->status == 1) {
            $shop = $product->user->shop;
            if (
                $shop->package_invalid_at == null
                || Carbon::now()->diffInDays(Carbon::parse($shop->package_invalid_at), false) < 0
                || $shop->product_upload_limit <= $shop->user->products()->where('published', 1)->count()
            ) {
                return 0;
            }
        }

        $product->save();

        Artisan::call('view:clear');
        Artisan::call('cache:clear');

        return 1;
    }

    public function updateProductApproval(Request $request)
    {
        $product = Product::findOrFail($request->id);
        $product->approved = $request->approved;

        if ($product->added_by == UserType::SELLER->value && addon_is_activated('seller_subscription')) {
            $shop = $product->user->shop;
            if (
                $shop->package_invalid_at == null
                || Carbon::now()->diffInDays(Carbon::parse($shop->package_invalid_at), false) < 0
                || $shop->product_upload_limit <= $shop->user->products()->where('published', 1)->count()
            ) {
                return 0;
            }
        }

        $product->save();

        $users = User::findMany($product->user_id);
        $data = [];
        $data['product_type'] = $product->digital == 0 ? 'physical' : 'digital';
        $data['status'] = $request->approved == 1 ? 'approved' : 'rejected';
        $data['product'] = $product;
        $data['notification_type_id'] = get_notification_type('seller_product_approved', 'type')->id;
        Notification::send($users, new ShopProductNotification($data));

        Artisan::call('view:clear');
        Artisan::call('cache:clear');

        return 1;
    }

    public function updateFeatured(Request $request)
    {
        $product = Product::findOrFail($request->id);
        $product->featured = $request->status;
        if ($product->save()) {
            Artisan::call('view:clear');
            Artisan::call('cache:clear');

            return 1;
        }

        return 0;
    }

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
                // foreach (json_decode($request[$name][0]) as $key => $item) {
                if (isset($request[$name])) {
                    $data = [];
                    foreach ($request[$name] as $key => $item) {
                        // array_push($data, $item->value);
                        array_push($data, $item);
                    }
                    array_push($options, $data);
                }
            }
        }

        $combinations = (new CombinationService)->generate_combination($options);

        return view(
            'backend.product.products.sku_combinations',
            compact('combinations', 'unit_price', 'colors_active', 'product_name')
        );
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
                // foreach (json_decode($request[$name][0]) as $key => $item) {
                if (isset($request[$name])) {
                    $data = [];
                    foreach ($request[$name] as $key => $item) {
                        // array_push($data, $item->value);
                        array_push($data, $item);
                    }
                    array_push($options, $data);
                }
            }
        }

        $combinations = (new CombinationService)->generate_combination($options);

        return view(
            'backend.product.products.sku_combinations_edit',
            compact('combinations', 'unit_price', 'colors_active', 'product_name', 'product')
        );
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

    public function setProductDiscount(Request $request)
    {
        return $this->productService->setCategoryWiseDiscount($request->except(['_token']));
    }
}
