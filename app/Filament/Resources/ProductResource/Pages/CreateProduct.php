<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use App\Models\User;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;

class CreateProduct extends CreateRecord
{
    protected static string $resource = ProductResource::class;

    /**
     * Mutate form data before creating the product record.
     * Sets `added_by`, `user_id`, `slug`, and converts `tags` array to comma-separated string.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $authUser = auth()->user();

        $data['added_by'] = $authUser->user_type === 'seller' ? 'seller' : 'admin';
        $data['user_id'] = $authUser->user_type === 'seller'
            ? $authUser->id
            : User::where('user_type', 'admin')->first()->id;

        // Auto-approve unless seller and setting requires admin approval
        if ($authUser->user_type === 'seller' && get_setting('product_approve_by_admin') == 1) {
            $data['approved'] = 0;
        } else {
            $data['approved'] = $data['approved'] ?? 1;
        }

        // Ensure slug is unique
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        $slug = $data['slug'];
        $count = \App\Models\Product::where('slug', 'LIKE', $slug.'%')->count();
        if ($count > 0) {
            $data['slug'] = $slug.'-'.($count + 1);
        }

        // Convert tags array to comma-separated string
        if (isset($data['tags']) && is_array($data['tags'])) {
            $data['tags'] = implode(',', $data['tags']);
        }

        // Set defaults for fields not in the form
        $data['colors'] = $data['colors'] ?? json_encode([]);
        $data['choice_options'] = $data['choice_options'] ?? json_encode([]);
        $data['attributes'] = $data['attributes'] ?? json_encode([]);

        return $data;
    }
}
