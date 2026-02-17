<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EditProduct extends EditRecord
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    /**
     * Handle the record update process.
     *
     * @param array{
     *     name?: string,
     *     slug?: string,
     *     brand_id?: int|string|null,
     *     categories?: int[]|string[],
     *     tags?: string|string[],
     *     description?: string|null,
     *     unit_price?: float|string,
     *     purchase_price?: float|string,
     *     discount?: float|string,
     *     discount_type?: string,
     *     current_stock?: int,
     *     shipping_type?: string,
     *     shipping_cost?: float|string,
     *     est_shipping_days?: int|null,
     *     meta_title?: string,
     *     meta_description?: string,
     *     published?: bool|int,
     *     has_warranty?: bool|int,
     *     thumbnail_img?: mixed,
     *     photos?: mixed,
     *     meta_img?: mixed,
     *     pdf?: mixed,
     *     colors?: string[],
     *     choice_no?: int[],
     *     choice_options?: array<int, array{name: string, values: string[]}>,
     *     stocks?: array<int, array{variant: string, price: float, sku: string, qty: int, image?: mixed}>
     * } $data
     *
     * @throws \Throwable
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Store tags for later saving
        $this->tags = $data['tags'] ?? [];

        // Product model is unguarded, so we must manually remove relationship fields
        // that shouldn't be saved as columns (especially since 'tags' column was removed).
        $data = collect($data)->except(['categories', 'tags'])->toArray();

        return $data;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        DB::beginTransaction();

        try {
            $record->update($data);

            if (! empty($this->tags)) {
                $record->syncTags($this->tags);
            }

            DB::commit();

            return $record;
        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('Product update failed: '.$e->getMessage(), [
                'user_id' => auth()->id(),
                'product_id' => $record->getKey(),
                'data' => $data,
                'tags' => $this->tags,
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }
}
