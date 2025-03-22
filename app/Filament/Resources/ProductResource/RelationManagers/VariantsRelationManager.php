<?php

namespace App\Filament\Resources\ProductResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use App\Models\Attribute;
use Filament\Tables\Table;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;

class VariantsRelationManager extends RelationManager
{
    protected static string $relationship = 'variants';

    protected static ?string $modelLabel = 'atributos';

    public function getTableHeading(): string
    {
        return 'Variaciones de atributos';
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Repeater::make('attributes')
                    ->label('Grupo')
                    ->relationship('attributes') // Relación con `AttributeVariant`
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('attribute_id')
                                    ->label('Nombre')
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
                                TextInput::make('value')
                                    ->label('Valor')
                                    ->required(),
                           ])
                    ])
                    ->collapsible()
                    ->reorderable()
                    ->extraAttributes(['class' => 'repeater-grid']),
            ]);
    }
    

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('id')
                    ->label('ID'),
                TextColumn::make('attributes.attribute.key')
                    ->label('Atributos')
                    ->searchable()
                    ->formatStateUsing(function ($record) {
                        // Obtener todas las combinaciones de key (de la tabla attributes) y value (de la tabla attribute_variants), luego unirlas en una sola línea
                        $record->load('attributes.attribute');
                        return $record->attributes->map(function ($attributeVariant) {
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
