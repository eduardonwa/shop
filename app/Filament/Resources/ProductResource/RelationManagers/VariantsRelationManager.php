<?php

namespace App\Filament\Resources\ProductResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use App\Models\Attribute;
use Filament\Tables\Table;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Repeater;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;

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
                Section::make('Info')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('total_variant_stock')
                                    ->label('Unidades')
                                    ->required()
                                    ->numeric()
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        // si el stock es 0, 'is_active' será false
                                        $set('is_active', $state != 0);
                                    }),
                                Toggle::make('is_active')
                                    ->label('Estado')
                                    ->inline(false)
                                    ->disabled(fn (callable $get) => $get('total_variant_stock') == 0),
                                SpatieMediaLibraryFileUpload::make('media') // 'media' es el nombre fijo para Spatie
                                    ->collection('product-variant-image') // Debe coincidir con tu colección
                                    ->label('Imagen de la variante')
                                    ->image(),
                            ])
                    ]),
                Repeater::make('attributes')
                    ->label('Grupo de atributos')
                    ->relationship('attributes')
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
                SpatieMediaLibraryImageColumn::make('media')
                    ->label('Imagen')
                    ->collection('product-variant-image')
                    ->size(50)
                    ->extraImgAttributes([
                        'style' => 'border-radius: 0.5rem;'
                    ]),
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
                TextColumn::make('total_variant_stock')
                    ->label('Inventario')
                    ->sortable(),
                TextColumn::make('is_active')
                    ->label('Estado')
                    ->sortable()
                    ->badge()
                    ->formatStateUsing(fn (bool $state): string => $state ? 'Activo' : 'Inactivo')
                    ->color(fn (bool $state): string => $state ? 'success' : 'danger'),
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
