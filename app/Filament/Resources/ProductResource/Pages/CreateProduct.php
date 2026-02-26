<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\DTOs\ProductDTO;
use App\Enums\UserType;
use App\Filament\Resources\ProductResource;
use App\Services\ProductService;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\CreateRecord\Concerns\Translatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CreateProduct extends CreateRecord
{
    use Translatable;

    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\LocaleSwitcher::make(),
        ];
    }

    /**
     * Inject server-side defaults before the record is created.
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();
        $data['added_by'] = UserType::ADMIN->value;

        return $data;
    }

    /**
     * Route creation through ProductService (Service-First pattern).
     *
     * The service handles pure business logic; this caller owns
     * the transaction, error handling, and logging.
     *
     * @throws \Throwable
     */
    protected function handleRecordCreation(array $data): Model
    {
        return DB::transaction(function () use ($data) {
            try {
                return app(ProductService::class)->store(
                    ProductDTO::fromArray($data)
                );
            } catch (\Throwable $e) {
                Log::error('Product creation failed', [
                    'user_id' => auth()->id(),
                    'data' => $data,
                    'trace' => $e->getTrace(),
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
