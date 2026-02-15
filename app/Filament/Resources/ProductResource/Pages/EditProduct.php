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
     * @param  array<string, mixed>  $data
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
