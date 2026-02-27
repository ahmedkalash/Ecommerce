<?php

namespace App\Filament\Resources\CategoryResource\Pages;

use App\Filament\Resources\CategoryResource;
use App\Services\CategoryService;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Pages\EditRecord\Concerns\Translatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EditCategory extends EditRecord
{
    use Translatable;

    protected static string $resource = CategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\LocaleSwitcher::make(),
            Actions\ViewAction::make(),
            Actions\DeleteAction::make()
                ->using(fn (\App\Models\Category $record) => app(CategoryService::class)->delete($record)),
        ];
    }

    /**
     * Route updates through CategoryService (Service-First pattern).
     *
     * The service handles pure business logic; this caller owns
     * the transaction, error handling, and logging.
     *
     * @param  array{
     *     name?: string,
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
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        return DB::transaction(function () use ($record, $data) {
            try {
                return app(CategoryService::class)->update($data, $record);
            } catch (\Throwable $e) {
                Log::error('Category update failed', [
                    'category_id' => $record->id,
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
