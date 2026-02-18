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

    /**
     * Inject server-side defaults before creation.
     *
     * @param  array<string, mixed>  $data  Raw form data from Filament.
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();
        $data['added_by'] = 'admin';

        return $data;
    }

    /**
     * Route creation through ProductService (Service-First pattern).
     *
     * The caller owns DB::transaction and error handling.
     * The service owns pure business logic only.
     */
    protected function handleRecordCreation(array $data): Model
    {
        return DB::transaction(function () use ($data) {
            try {
                return app(ProductService::class)->store(
                    ProductData::fromArray($data)
                );
            } catch (\Throwable $e) {
                Log::error('Product creation failed', [
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
