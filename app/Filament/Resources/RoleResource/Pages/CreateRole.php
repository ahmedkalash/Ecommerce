<?php

namespace App\Filament\Resources\RoleResource\Pages;

use App\DTOs\RoleDTO;
use App\Filament\Resources\RoleResource;
use App\Services\RoleService;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CreateRole extends CreateRecord
{
    protected static string $resource = RoleResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        try {
            return DB::transaction(function () {
                return app(RoleService::class)->store(RoleDTO::fromArray($this->data));
            });
        } catch (\Exception $e) {
            Log::error('Failed to create role: '.$e->getMessage(), ['trace' => $e->getTraceAsString()]);

            Notification::make()
                ->title('Error creating role')
                ->body('An error occurred while creating the role. Please try again.')
                ->danger()
                ->send();

            $this->halt();
        }
    }
}
