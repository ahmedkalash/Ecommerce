<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use App\Services\ProductService;
use Exception;
use Filament\Actions;
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
            Actions\DeleteAction::make(),
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
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        return DB::transaction(function () use ($record, $data) {
            try {
                /** @var ProductService $productService */
                $productService = app(ProductService::class);

                return $productService->update($data, $record);
            } catch (Exception $e) {
                Log::error('Product update failed (Filament): '.$e->getMessage(), [
                    'product_id' => $record->id,
                    'trace' => $e->getTraceAsString(),
                    'data' => $data,
                ]);

                throw $e;
            }
        });
    }

    /**
     * Mutate form data before filling the form on edit.
     * Converts comma-separated tags string back to an array for the TagsInput component.
     *
     * @param  array{
     *      tags?: string|string[],
     *      [key: string]: mixed
     *  }  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Convert comma-separated tags to array for TagsInput
        if (isset($data['tags']) && is_string($data['tags'])) {
            $data['tags'] = array_filter(explode(',', $data['tags']));
        }

        // choice_options and stocks are handled by Filament via relationships/casts automatically.

        return $data;
    }
}
