<?php

namespace App\Filament\Resources\StaffResource\Pages;

use App\DTOs\StaffDTO;
use App\Filament\Resources\StaffResource;
use App\Models\Admin;
use App\Services\StaffService;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EditStaff extends EditRecord
{
    protected static string $resource = StaffResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\CreateAction::make()
                ->label('Create New'),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {

        // Hydrate roles so Filament's multi-select displays them
        $data['roles'] = $this->record->roles->pluck('id')->toArray();

        return $data;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        try {
            return DB::transaction(function () use ($record, $data) {
                /** @var Admin $record */
                app(StaffService::class)->update(StaffDTO::fromArray($data), $record);

                return $record->fresh();
            });
        } catch (\Exception $e) {
            Log::error('Failed to update staff member: '.$e->getMessage(), ['trace' => $e->getTraceAsString()]);

            Notification::make()
                ->title('Error updating staff')
                ->body('An error occurred while updating the staff member. Please try again.')
                ->danger()
                ->send();

            $this->halt();
        }
    }
}
