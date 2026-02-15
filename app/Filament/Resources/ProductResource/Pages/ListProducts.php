<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\MaxWidth;

class ListProducts extends ListRecords
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getMaxContentWidth(): MaxWidth
    {
        return MaxWidth::Full; // 👈 This makes ONLY this list page full-width
    }
}
