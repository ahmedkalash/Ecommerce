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

    /**
     * Hydrate relationship data into the form.
     *
     * Since we don't use ->relationship() on categories or stocks,
     * Filament won't auto-load them. We manually inject them here.
     *
     * @param  array<string, mixed>  $data  Model attributes from Filament.
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['category_ids'] = $this->record->categories->pluck('id')->toArray();
        $data['stocks'] = $this->record->stocks->map(fn ($stock) => $stock->toArray())->toArray();

        return $data;
    }

    /**
     * Route update through ProductService (Service-First pattern).
     *
     * The caller owns DB::transaction and error handling.
     * The service owns pure business logic only.
     */
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        return DB::transaction(function () use ($record, $data) {
            try {
                app(ProductService::class)->update(
                    ProductData::fromArray($data),
                    $record
                );

                return $record->fresh();
            } catch (\Throwable $e) {
                Log::error('Product update failed', [
                    'product_id' => $record->id,
                    'user_id' => auth()->id(),
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
