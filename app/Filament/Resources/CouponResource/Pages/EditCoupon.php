<?php

namespace App\Filament\Resources\CouponResource\Pages;

use App\DTOs\CouponDTO;
use App\Filament\Resources\CouponResource;
use App\Models\Coupon;
use App\Services\CouponService;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class EditCoupon extends EditRecord
{
    protected static string $resource = CouponResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\CreateAction::make()->label('Create New'),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // product_ids is cast to array on the model, so it just works
        // However, Filament's Select with `multiple()` usually expects an array of strings/ints.
        // It's already an array, so we don't need to manually json_decode it,
        // but we ensure it's not null.
        $data['product_ids'] = $data['product_ids'] ?? [];

        return $data;
    }

    /**
     * @throws \Throwable
     */
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        /** @var Coupon $record */
        return DB::transaction(function () use ($record, $data) {

            return app(CouponService::class)->update($record, CouponDTO::fromArray($data));
        });
    }
}
