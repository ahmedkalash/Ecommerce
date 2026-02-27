<?php

namespace App\Filament\Resources\CouponResource\Pages;

use App\DTOs\CouponDTO;
use App\Filament\Resources\CouponResource;
use App\Services\CouponService;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\CreateRecord\Concerns\Translatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CreateCoupon extends CreateRecord
{
    use Translatable;

    protected static string $resource = CouponResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\LocaleSwitcher::make(),
        ];
    }

    /**
     * @throws \Throwable
     */
    protected function handleRecordCreation(array $data): Model
    {
        return DB::transaction(function () use ($data) {
            $dto = CouponDTO::fromArray($data);

            return app(CouponService::class)->create($dto);
        });
    }
}
