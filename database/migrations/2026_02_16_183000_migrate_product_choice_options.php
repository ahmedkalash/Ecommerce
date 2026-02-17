<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Map legacy Attribute IDs to new Schemaless Keys
        // Based on dump: 1 -> Size, 2 -> Fabric
        $map = [
            1 => 'size',
            2 => 'fabric',
        ];

        // We use DB facade to avoid Model casting issues and ensure speed
        DB::table('products')->chunkById(100, function ($products) use ($map) {
            foreach ($products as $product) {
                if (empty($product->choice_options)) {
                    continue;
                }

                $options = json_decode($product->choice_options, true);

                // If double encoded or invalid
                if (! is_array($options)) {
                    // Try decoding again if string
                    if (is_string($options)) {
                        $options = json_decode($options, true);
                    }
                }

                if (! is_array($options)) {
                    continue;
                }

                $changed = false;
                $newOptions = [];

                foreach ($options as $option) {
                    // Structure: {"attribute_id": "1", "values": ["S", "M"]}
                    if (isset($option['attribute_id'])) {
                        $id = $option['attribute_id'];
                        if (isset($map[$id])) {
                            $option['attribute_id'] = $map[$id]; // Change 1 to "size"
                            $changed = true;
                        }
                    }
                    $newOptions[] = $option;
                }

                if ($changed) {
                    DB::table('products')
                        ->where('id', $product->id)
                        ->update(['choice_options' => json_encode($newOptions)]);
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverse mapping if needed
        $map = [
            'size' => 1,
            'fabric' => 2,
        ];

        DB::table('products')->chunkById(100, function ($products) use ($map) {
            foreach ($products as $product) {
                if (empty($product->choice_options)) {
                    continue;
                }
                $options = json_decode($product->choice_options, true);
                if (! is_array($options)) {
                    continue;
                }

                $changed = false;
                $newOptions = [];

                foreach ($options as $option) {
                    if (isset($option['attribute_id'])) {
                        $id = $option['attribute_id'];
                        if (isset($map[$id])) {
                            $option['attribute_id'] = $map[$id];
                            $changed = true;
                        }
                    }
                    $newOptions[] = $option;
                }

                if ($changed) {
                    DB::table('products')
                        ->where('id', $product->id)
                        ->update(['choice_options' => json_encode($newOptions)]);
                }
            }
        });
    }
};
