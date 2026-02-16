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
     * @param array{
     *     name: string,
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
        return $this->getResource()::getUrl('edit', ['record' => $this->record]);
    }
}
