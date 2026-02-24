<?php

namespace App\Filament\Resources\StaffResource\Pages;

use App\DTOs\StaffDTO;
use App\Filament\Resources\StaffResource;
use App\Services\StaffService;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class CreateStaff extends CreateRecord
{
    protected static string $resource = StaffResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        try {
            return DB::transaction(function () use ($data) {
                // We use app() to resolve the service so it can be easily mocked in tests if needed
                return app(StaffService::class)->store(StaffDTO::fromArray($data));
            });
        } catch (Throwable $e) {
            Log::error('Failed to create staff member: '.$e->getMessage(), ['trace' => $e->getTraceAsString()]);

            Notification::make()
                ->title('Error creating staff')
                ->body('An error occurred while creating the staff member. Please try again.')
                ->danger()
                ->send();

            $this->halt();
        }
    }
}
