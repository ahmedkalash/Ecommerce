<?php

namespace App\Http\Controllers;

use App\Models\AttributeValue;
use App\Models\Category;
use App\Models\CategoryTranslation;
use App\Models\CustomerProduct;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductStock;
use App\Models\ProductTax;
use App\Models\SubCategory;
use App\Models\SubCategoryTranslation;
use App\Models\SubSubCategory;
use App\Models\SubSubCategoryTranslation;
use App\Models\Tax;
use Artisan;
use DB;
use File;
use Illuminate\Http\Request;
use Schema;
use ZipArchive;

class DemoController extends Controller
{
    public function __construct()
    {
        ini_set('memory_limit', '2048M');
        ini_set('max_execution_time', 600);
    }

    public function cron_1()
    {
        if (env('DEMO_MODE') != 'On') {
            return back();
        }
        $this->drop_all_tables();
        $this->import_demo_sql();
    }

    public function cron_2()
    {
        // if (env('DEMO_MODE') != 'On') {
        //     return back();
        // }
        // $this->remove_folder();
        // $this->extract_uploads();
    }

    public function drop_all_tables()
    {
        Schema::disableForeignKeyConstraints();
        foreach (DB::select('SHOW TABLES') as $table) {
            $table_array = get_object_vars($table);
            Schema::drop($table_array[key($table_array)]);
        }
    }

    public function import_demo_sql()
    {
        Artisan::call('cache:clear');
        $sql_path = base_path('demo.sql');
        DB::unprepared(file_get_contents($sql_path));
    }

    public function extract_uploads()
    {
        $zip = new ZipArchive;
        $zip->open(base_path('public/uploads.zip'));
        $zip->extractTo('public/uploads');
    }

    public function remove_folder()
    {
        File::deleteDirectory(base_path('public/uploads'));
    }

    public function migrate_attribute_values(Request $request)
    {
        foreach (Product::all() as $product) {
            if ($product->variant_product) {
                try {
                    $choice_options = json_decode($product->choice_options);
                    foreach ($choice_options as $choice_option) {
                        foreach ($choice_option->values as $value) {
                            $attribute_value = AttributeValue::where('value', $value)->first();
                            if ($attribute_value == null) {
                                $attribute_value = new AttributeValue;
                                $attribute_value->attribute_id = $choice_option->attribute_id;
                                $attribute_value->value = $value;
                                $attribute_value->save();
                            }
                        }
                    }
                } catch (\Exception $e) {
                }
            }
        }
    }

    public function convertTaxes()
    {
        $tax = Tax::first();

        foreach (Product::all() as $product) {
            $product_tax = new ProductTax;
            $product_tax->product_id = $product->id;
            $product_tax->tax_id = $tax->id;
            $product_tax->tax = $product->tax;
            $product_tax->tax_type = $product->tax_type;
            $product_tax->save();
        }
    }

    public function convert_assets(Request $request)
    {
        // This method relies on the legacy Upload model which has been removed.
        // It should be refactored to use Spatie Media Library if asset conversion is still required.
        return back();
    }

    public function convert_category()
    {
        foreach (SubCategory::all() as $key => $value) {
            $category = new Category;
            $parent = Category::find($value->category_id);

            $category->name = $value->name;
            $category->digital = $parent->digital;
            $category->banner = null;
            $category->icon = null;
            $category->meta_title = $value->meta_title;
            $category->meta_description = $value->meta_description;

            $category->parent_id = $parent->id;
            $category->slug = $value->slug;
            $category->commision_rate = $parent->commision_rate;

            $category->save();

            foreach (SubCategoryTranslation::where('sub_category_id', $value->id)->get() as $translation) {
                $category_translation = new CategoryTranslation;
                $category_translation->category_id = $category->id;
                $category_translation->lang = $translation->lang;
                $category_translation->name = $translation->name;
                $category_translation->save();
            }
        }

        foreach (SubSubCategory::all() as $key => $value) {
            $category = new Category;
            $parent = Category::find(Category::where('name', SubCategory::find($value->sub_category_id)->name)->first()->id);

            $category->name = $value->name;
            $category->digital = $parent->digital;
            $category->banner = null;
            $category->icon = null;
            $category->meta_title = $value->meta_title;
            $category->meta_description = $value->meta_description;

            $category->parent_id = $parent->id;
            $category->slug = $value->slug;
            $category->commision_rate = $parent->commision_rate;

            $category->save();

            foreach (SubSubCategoryTranslation::where('sub_sub_category_id', $value->id)->get() as $translation) {
                $category_translation = new CategoryTranslation;
                $category_translation->category_id = $category->id;
                $category_translation->lang = $translation->lang;
                $category_translation->name = $translation->name;
                $category_translation->save();
            }
        }

        foreach (Product::all() as $key => $value) {
            try {
                if ($value->subsubcategory_id == null) {
                    $value->category_id = Category::where('name', SubCategory::find($value->subcategory_id)->name)->first()->id;
                    $value->save();
                } else {
                    $value->category_id = Category::where('name', SubSubCategory::find($value->subsubcategory_id)->name)->first()->id;
                    $value->save();
                }
            } catch (\Exception $e) {
            }
        }

        foreach (CustomerProduct::all() as $key => $value) {
            try {
                if ($value->subsubcategory_id == null) {
                    $value->category_id = Category::where('name', SubCategory::find($value->subcategory_id)->name)->first()->id;
                    $value->save();
                } else {
                    $value->category_id = Category::where('name', SubSubCategory::find($value->subsubcategory_id)->name)->first()->id;
                    $value->save();
                }
            } catch (\Exception $e) {
            }
        }

        // foreach (Product::all() as $key => $product) {
        //     if (is_array(json_decode($product->tags))) {
        //         $tags = array();
        //         foreach (json_decode($product->tags) as $tag) {
        //             array_push($tags, $tag->value);
        //         }
        //         $product->tags = implode(',', $tags);
        //         $product->save();
        //     }
        // }
    }

    public function insert_product_variant_forcefully(Request $request)
    {
        foreach (Product::all() as $product) {
            if ($product->stocks->isEmpty()) {
                $product_stock = new ProductStock;
                $product_stock->product_id = $product->id;
                $product_stock->variant = '';
                $product_stock->price = $product->unit_price;
                $product_stock->sku = $product->sku;
                $product_stock->qty = $product->current_stock;
                $product_stock->save();
            }
        }
    }

    public function update_seller_id_in_orders($id_min, $id_max)
    {
        $orders = Order::where('id', '>=', $id_min)->where('id', '<=', $id_max)->get();

        foreach ($orders as $order) {
            $this->update_seller_id_in_order($order);
        }
    }

    public function update_seller_id_in_order($order)
    {
        if ($order->seller_id == 0) {
            // dd($order->orderDetails[0]->seller_id);
            $order->seller_id = $order->orderDetails[0]->seller_id;
            $order->save();
        }
    }

    public function setCategoryToProductCategory()
    {
        $products = Product::all();
        $new_product_array = [];
        foreach ($products as $product) {
            $new_product_array[] = [
                'product_id' => $product->id,
                'category_id' => $product->category_id,
            ];
        }
        $collection = collect($new_product_array);
        $chunks = $collection->chunk(500);

        foreach ($chunks as $chunk) {
            ProductCategory::insert($chunk->toArray());
        }
    }
}
