<?php

namespace App\Filament\Resources\ProductVariantResource\Pages;

use Filament\Actions;
use Filament\Support\Enums\Alignment;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\ProductVariantResource;

class EditProductVariant extends EditRecord
{
    protected static string $resource = ProductVariantResource::class;

    public static string | Alignment $formActionsAlignment = Alignment::Right;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
