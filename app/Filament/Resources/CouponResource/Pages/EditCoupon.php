<?php

namespace App\Filament\Resources\CouponResource\Pages;

use Filament\Actions;
use Filament\Support\Enums\Alignment;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\CouponResource;

class EditCoupon extends EditRecord
{
    protected static string $resource = CouponResource::class;
    
    public static string | Alignment $formActionsAlignment = Alignment::Right;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
