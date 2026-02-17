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
        $map = [
            1 => 'size',
            2 => 'fabric',
        ];

        DB::table('products')->chunkById(100, function ($products) use ($map) {
            foreach ($products as $product) {
                if (empty($product->attributes)) {
                    continue;
                }

                $attributes = json_decode($product->attributes, true);
                if (! is_array($attributes)) {
                    continue;
                }

                $changed = false;
                $newAttributes = [];

                foreach ($attributes as $attr) {
                    // Check if it is numeric (old ID)
                    if (is_numeric($attr) && isset($map[$attr])) {
                        $newAttributes[] = $map[$attr];
                        $changed = true;
                    } else {
                        $newAttributes[] = $attr;
                    }
                }

                if ($changed) {
                    DB::table('products')
                        ->where('id', $product->id)
                        ->update(['attributes' => json_encode($newAttributes)]);
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No reverse needed as we can support both if we really wanted to,
        // but strictly speaking we should map back.
        // For now, leaving empty as this is a forward-fix.
    }
};
