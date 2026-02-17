<?php

namespace App\Services;

use AizPackages\CombinationGenerate\Services\CombinationService;
use App\Models\Product;

class ProductStockService
{
    public function store(array $data, Product $product): void
    {
        $options = [];
        $colors_active = isset($data['colors_active']) && $data['colors_active'] == '1';

        // 1. Prepare Options for Combination Generation
        if ($colors_active && isset($data['colors']) && count($data['colors']) > 0) {
            array_push($options, $data['colors']);
        }

        if (isset($data['choice_no'])) {
            foreach ($data['choice_no'] as $key => $no) {
                $name = 'choice_options_'.$no;
                if (isset($data[$name])) {
                    array_push($options, $data[$name]);
                }
            }
        }

        // 2. Generate Combinations
        $combinations = (new CombinationService)->generate_combination($options);

        // 3. Save Stocks
        if (count($combinations) > 0) {
            foreach ($combinations as $key => $combination) {
                $str = '';
                $extra_attributes = [];
                $option_index = 0;

                // Build Variant String and Extra Attributes
                foreach ($combination as $k => $item) {
                    if ($k == 0 && $colors_active) {
                        // Color processing
                        $colors = config('attributes.presets.color.options', []);
                        $color_name = array_search($item, $colors);
                        if (! $color_name) {
                            $color_name = $item;
                        }

                        $str .= $color_name;
                        $extra_attributes['color'] = $color_name; // Store Name
                        $option_index++; // Move to next option source (choice_no)
                    } else {
                        // Choice Option processing
                        if (strlen($str) > 0) {
                            $str .= '-'.str_replace(' ', '', $item);
                        } else {
                            $str .= str_replace(' ', '', $item);
                        }
                    }
                }

                // Map remaining items to choice keys
                if (isset($data['choice_no'])) {
                    foreach ($data['choice_no'] as $param_key) { // param_key is index? No, param_key is index in loop, $no is value 'size'
                        // choice_no comes from request as array.
                        // Controller: `foreach ($request->choice_no as $key => $no)`
                        // If color active, it consumed $combination[0].
                        // So we map $combination[$k] (where k maps to choice_no position).

                        // Re-do mapping logic cleaner:
                        // We have exact mapping order: [Color?, Choice1, Choice2...] matches Combination [Item0, Item1...]
                        // So we can iterate together.
                    }
                }

                // Let's restart metadata loop to be precise
                $combo_idx = 0;
                if ($colors_active) {
                    $item = $combination[$combo_idx];
                    $colors = config('attributes.presets.color.options', []);
                    $color_name = array_search($item, $colors);
                    if (! $color_name) {
                        $color_name = $item;
                    }

                    // We already added to $str above
                    $extra_attributes['color'] = $color_name;
                    $combo_idx++;
                }

                if (isset($data['choice_no'])) {
                    foreach ($data['choice_no'] as $choice_key) {
                        if (isset($combination[$combo_idx])) {
                            $extra_attributes[$choice_key] = $combination[$combo_idx];
                            $combo_idx++;
                        }
                    }
                }

                $stock = $product->stocks()->firstOrNew(['variant' => $str]);

                $stock->price = (float) ($data['price_'.$str] ?? 0);
                $stock->sku = $data['sku_'.$str] ?? null;
                $stock->qty = (int) ($data['qty_'.$str] ?? 0);
                $stock->image = $data['img_'.$str] ?? null;

                // Discount Handling
                $stock->discount = (float) ($data['discount_'.$str] ?? 0);
                $stock->discount_type = $data['discount_type_'.$str] ?? 'amount';
                $stock->discount_start_date = isset($data['discount_start_date_'.$str]) ? (int) $data['discount_start_date_'.$str] : null;
                $stock->discount_end_date = isset($data['discount_end_date_'.$str]) ? (int) $data['discount_end_date_'.$str] : null;

                // Tax Handling
                $stock->tax = (float) ($data['tax_'.$str] ?? 0);
                $stock->tax_type = $data['tax_type_'.$str] ?? 'amount';

                // Weight Handling
                $stock->weight = (float) ($data['weight_'.$str] ?? 0);

                // Schemaless Attributes assignment
                // Assuming $stock uses SchemalessAttributesTrait
                if (isset($stock->extra_attributes)) {
                    foreach ($extra_attributes as $ek => $ev) {
                        $stock->extra_attributes->$ek = $ev;
                    }
                }

                $stock->save();
            }
        } else {
            $stock = $product->stocks()->firstOrNew(['variant' => '']);
            $stock->price = (float) ($data['unit_price'] ?? 0);
            $stock->sku = $data['sku'] ?? null;
            $stock->qty = (int) ($data['current_stock'] ?? 0);

            // Simple Product Discount Handling
            $stock->discount = (float) ($data['discount'] ?? 0);
            $stock->discount_type = $data['discount_type'] ?? 'amount';

            // Handle date_range string or separate start/end
            if (isset($data['date_range'])) {
                $date_range = explode(' to ', $data['date_range']);
                if (count($date_range) == 2) {
                    $stock->discount_start_date = strtotime($date_range[0]);
                    $stock->discount_end_date = strtotime($date_range[1]);
                }
            } else {
                $stock->discount_start_date = isset($data['discount_start_date']) ? (int) $data['discount_start_date'] : null;
                $stock->discount_end_date = isset($data['discount_end_date']) ? (int) $data['discount_end_date'] : null;
            }

            // Simple Product Tax Handling
            $stock->tax = (float) ($data['tax'] ?? 0);
            $stock->tax_type = $data['tax_type'] ?? 'amount';

            // Weight Handling
            $stock->weight = (float) ($data['weight'] ?? 0);

            $stock->save();
        }
    }

    public function product_duplicate_store(iterable $stocks, Product $new_product): void
    {
        foreach ($stocks as $stock) {
            $new_stock = $stock->replicate();
            $new_stock->product_id = $new_product->id;
            $new_stock->save();
        }
    }
}
