<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\DTOs\ProductDTO;
use App\Filament\Resources\ProductResource;
use App\Models\Product;
use App\Services\ProductService;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Pages\EditRecord\Concerns\Translatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EditProduct extends EditRecord
{
    use Translatable;

    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\LocaleSwitcher::make(),
            Actions\DeleteAction::make(),
            Actions\CreateAction::make()->label('Create New'),
        ];
    }

    /**
     * Hydrate data into the form.
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['stocks'] = $this->record->stocks->toArray();

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        return $data;
    }

    /**
     * Route updates through ProductService (Service-First pattern).
     *
     * The service handles pure business logic; this caller owns
     * the transaction, error handling, and logging.
     */
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        /** @var Product $record */
        return DB::transaction(function () use ($record, $data) {
            try {
                app(ProductService::class)->update(
                    ProductDTO::fromArray($data),
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
