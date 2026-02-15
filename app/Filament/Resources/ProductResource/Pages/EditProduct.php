<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProduct extends EditRecord
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    /**
     * Mutate form data before filling the form on edit.
     * Converts comma-separated tags string back to an array for the TagsInput component.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Convert comma-separated tags to array for TagsInput
        if (isset($data['tags']) && is_string($data['tags'])) {
            $data['tags'] = array_filter(explode(',', $data['tags']));
        }

        return $data;
    }

    /**
     * Mutate form data before saving the product record.
     * Converts tags array back to comma-separated string for storage.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Convert tags array to comma-separated string
        if (isset($data['tags']) && is_array($data['tags'])) {
            $data['tags'] = implode(',', $data['tags']);
        }

        return $data;
    }
}
