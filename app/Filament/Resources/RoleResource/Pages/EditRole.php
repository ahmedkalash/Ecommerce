<?php

namespace App\Filament\Resources\RoleResource\Pages;

use App\DTOs\RoleDTO;
use App\Filament\Resources\RoleResource;
use App\Services\RoleService;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;

class EditRole extends EditRecord
{
    protected static string $resource = RoleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        try {
            return DB::transaction(function () use ($record, $data) {
                /** @var Role $record */
                app(RoleService::class)->update(RoleDTO::fromArray($data), $record);

                return $record->fresh();
            });
        } catch (\Exception $e) {
            Log::error('Failed to update role: '.$e->getMessage(), ['trace' => $e->getTraceAsString()]);

            Notification::make()
                ->title('Error updating role')
                ->body('An error occurred while updating the role. Please try again.')
                ->danger()
                ->send();

            $this->halt();
        }
    }
}
