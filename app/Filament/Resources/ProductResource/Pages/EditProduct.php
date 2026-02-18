<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\DataTransferObjects\ProductData;
use App\Filament\Resources\ProductResource;
use App\Services\ProductService;
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
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        return DB::transaction(function () use ($record, $data) {
            try {
                // Map Filament raw data to ProductData DTO
                $productData = ProductData::fromArray($data);

                // Use ProductService for update
                $service = app(ProductService::class);
                $service->update($productData, $record);

                return $record;
            } catch (\Throwable $e) {
                Log::error('Product update failed (Filament): '.$e->getMessage(), [
                    'product_id' => $record->id,
                    'user_id' => auth()->id(),
                    'data' => $data,
                    'trace' => $e->getTraceAsString(),
                ]);

                throw $e;
            }
        });
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
