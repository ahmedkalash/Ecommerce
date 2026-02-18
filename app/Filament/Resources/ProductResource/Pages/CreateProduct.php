<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\DataTransferObjects\ProductData;
use App\Filament\Resources\ProductResource;
use App\Services\ProductService;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CreateProduct extends CreateRecord
{
    protected static string $resource = ProductResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        return DB::transaction(function () use ($data) {
            try {
                // Map Filament raw data to ProductData DTO
                $productData = ProductData::fromArray($data);

                // Use ProductService for creation to ensure all business logic is applied
                $service = app(ProductService::class);
                $record = $service->store($productData);

                // Handle Media (Filament Spatie Media Library component handles this
                // but we must ensure it's synced if the service didn't)
                // Actually, Filament's SpatieMediaLibraryFileUpload handles it after save.

                return $record;
            } catch (\Throwable $e) {
                Log::error('Product creation failed (Filament): '.$e->getMessage(), [
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
        return $this->getResource()::getUrl('edit', ['record' => $this->record]);
    }
}
