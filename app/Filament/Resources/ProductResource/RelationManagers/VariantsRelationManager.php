<?php

namespace App\Filament\Resources\ProductResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use App\Models\Attribute;
use Filament\Tables\Table;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;

class VariantsRelationManager extends RelationManager
{
    protected static string $relationship = 'variants';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Repeater::make('attributes') // Nombre de la relación en el modelo
                    ->label('Atributos')
                    ->relationship('attributes') // Relación con `AttributeVariant`
                    ->schema([
                        Forms\Components\Select::make('attribute_id')
                            ->label('Atributo')
                            ->relationship('attribute', 'key') // Relación con `attributes` (singular)
                            ->searchable()
                            ->createOptionForm([
                                TextInput::make('key')
                                    ->label('Nuevo atributo')
                                    ->required(),
                            ])
                            ->createOptionUsing(function (array $data) {
                                // Crear una nueva clave en la tabla attributes
                                return Attribute::create(['key' => $data['key']])->id;
                            }),
                        Forms\Components\TextInput::make('value')
                            ->label('Valor')
                            ->required(),
                    ])
                    ->collapsible()
                    ->reorderable()
                    ->grid(2), // Muestra en 2 columnas
            ]);
    }
    

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID'),
                Tables\Columns\TextColumn::make('attributes.attribute.key')
                    ->label('Atributos')
                    ->searchable()
                    ->formatStateUsing(function ($record) {
                        // Cargar las relaciones
                        $record->load('attributes.attribute'); 
                        // Obtener todas las combinaciones de key (de la tabla attributes) y value (de la tabla attribute_variants), luego unirlas en una sola línea
                        return $record->attributes->map(function ($attributeVariant) {
                            // Acceder a los atributos y valores correctamente desde la relación
                            return "{$attributeVariant->attribute->key}: {$attributeVariant->value}";
                        })->join(', ') ?? 'No hay atributos';
                    }),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }    
}
