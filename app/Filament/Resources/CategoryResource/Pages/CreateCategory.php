<?php

namespace App\Filament\Resources\CategoryResource\Pages;

use App\Filament\Resources\CategoryResource;
use App\Services\CategoryService;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\CreateRecord\Concerns\Translatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CreateCategory extends CreateRecord
{
    use Translatable;

    protected static string $resource = CategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\LocaleSwitcher::make(),
        ];
    }

    /**
     * Route creation through CategoryService (Service-First pattern).
     *
     * The service handles pure business logic; this caller owns
     * the transaction, error handling, and logging.
     *
     * @param  array{
     *     name: string,
     *     slug?: string,
     *     parent_id?: int|string|null,
     *     commision_rate?: float|string,
     *     featured?: bool,
     *     top?: bool,
     *     digital?: bool,
     *     meta_title?: string,
     *     meta_description?: string,
     *     refund_request_time?: int
     * }  $data
     */
    protected function handleRecordCreation(array $data): Model
    {
        return DB::transaction(function () use ($data) {
            try {
                return app(CategoryService::class)->store($data);
            } catch (\Throwable $e) {
                Log::error('Category creation failed', [
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
