<?php

namespace App\Filament\Resources\CouponResource\Pages;

use Filament\Support\Enums\Alignment;
use App\Filament\Resources\CouponResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCoupon extends CreateRecord
{
    protected static string $resource = CouponResource::class;

    public static string | Alignment $formActionsAlignment = Alignment::Right;

}
