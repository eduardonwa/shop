<?php

namespace App\Filament\Resources\CollectionResource\Pages;

use Filament\Actions;
use Filament\Support\Enums\Alignment;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\CollectionResource;

class CreateCollection extends CreateRecord
{
    protected static string $resource = CollectionResource::class;

    public static string | Alignment $formActionsAlignment = Alignment::Center;
}
