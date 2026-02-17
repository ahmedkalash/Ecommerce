<?php

namespace App\Filament\Resources\CategoryResource\Pages;

use App\Filament\Resources\CategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCategory extends EditRecord
{
    protected static string $resource = CategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make()
                ->using(fn (\App\Models\Category $record) => app(\App\Services\CategoryService::class)->delete($record)),
        ];
    }

    /**
     * @param array{
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
     * } $data
     */
    protected function handleRecordUpdate(\Illuminate\Database\Eloquent\Model $record, array $data): \Illuminate\Database\Eloquent\Model
    {
        return app(\App\Services\CategoryService::class)->update($data, $record);
    }
}
