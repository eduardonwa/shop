<?php

namespace App\Filament\Resources\CollectionResource\Pages;

use Filament\Actions;
use Filament\Support\Enums\Alignment;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\CollectionResource;

class EditCollection extends EditRecord
{
    protected static string $resource = CollectionResource::class;

    public static string | Alignment $formActionsAlignment = Alignment::Center;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
