<?php

namespace App\Filament\Resources\CollectionResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Arr;
use Filament\Tables\Columns\TextColumn;
use App\Filament\Fields\ProductSelector;
use Filament\Tables\Columns\ImageColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;

class ProductsRelationManager extends RelationManager
{
    protected static string $relationship = 'products';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                ProductSelector::make('selected_products')
                    ->label('Seleccionar productos (máximo 10)')
                    ->rules(['array', 'max:10'])
                    ->afterStateHydrated(function (ProductSelector $component, $state) {
                        // convertir string JSON a array si es necesario
                        if (is_string($state)) {
                            $stat = json_decode($state, true) ?? [];
                        }
                        $component->state((array) $state); 
                    })
                    ->dehydrateStateUsing(fn ($state) => is_array($state) ? $state : [])
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image.path')->label('Imagen'),
                Tables\Columns\TextColumn::make('name')->label('Nombre'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->form([
                        ProductSelector::make('selected_products')
                            ->label('Productos')
                            ->required()
                    ])
                    ->action(function (array $data) {
                        $this->handleRecordCreation($data);
                    })
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->form([
                        ProductSelector::make('selected_products')
                            ->label('Productos')
                            ->required()
                    ])
                    ->using(function (array $data, $record) {
                        $this->handleRecordUpdate($record, $data);
                    }),
                Tables\Actions\DetachAction::make()
                    ->label('Quitar')
            ]);
    }

    protected function handleRecordCreation(array $data): void
    {
        $productIds = $this->normalizeProductIds($data['selected_products'] ?? []);
        $this->getOwnerRecord()->products()->syncWithoutDetaching($productIds);
    }

    protected function handleRecordUpdate($record, array $data): void
    {
        $productIds = $this->normalizeProductIds($data['selected_products'] ?? []);
        $this->getOwnerRecord()->products()->syncWithoutDetaching($productIds);
    }

    private function normalizeProductIds($ids): array
    {
        if (is_string($ids)) {
            $ids = json_decode($ids, true) ?? [];
        }
        
        return array_filter((array) $ids, fn($id) => !empty($id));
    }    
}
