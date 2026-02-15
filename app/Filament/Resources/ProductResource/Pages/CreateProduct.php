<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use App\Services\ProductService;
use Exception;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CreateProduct extends CreateRecord
{
    protected static string $resource = ProductResource::class;

    /**
     * @throws \Throwable
     */
    /**
     * Handle the record creation process.
     * Delegates the logic to ProductService but maintains responsibility for
     * transaction management and UI-level error logging.
     *
     *
     * @throws \Throwable
     */
    protected function handleRecordCreation(array $data): Model
    {
        return DB::transaction(function () use ($data) {
            try {
                /** @var ProductService $productService */
                $productService = app(ProductService::class);

                return $productService->store($data);
            } catch (Exception $e) {
                Log::error('Product creation failed (Filament): '.$e->getMessage(), [
                    'trace' => $e->getTraceAsString(),
                    'data' => $data,
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
