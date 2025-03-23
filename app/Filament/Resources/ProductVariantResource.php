<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use App\Models\Attribute;
use Filament\Tables\Table;
use App\Models\ProductVariant;
use Filament\Resources\Resource;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\KeyValue;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Actions\Action;
use App\Filament\Resources\ProductVariantResource\Pages;

class ProductVariantResource extends Resource
{
    protected static ?string $model = ProductVariant::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getModelLabel(): string
    {
        return 'Variaciones de producto';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(1)
                    ->schema([
                        Placeholder::make('product_name')
                        ->label('Producto')
                        ->content(function ($record) {
                            return $record->product->name; // desde la relación de "product" iteramos y obtenemos el nombre de los productos
                        }),
                    Hidden::make('product_id')
                        ->default(fn () => request('product_id')),
                    KeyValue::make('attributes')
                        ->label('Atributos')
                        ->keyLabel('Nombre')
                        ->valueLabel('Valor')
                        ->dehydrated(false)
                        ->afterStateHydrated(function ($component, $record) {
                            // Convertir los atributos existentes a un array clave-valor
                            $attributes = $record->attributes->pluck('value', 'attribute.key')->toArray();
                            $component->state($attributes);
                        })
                        ->afterStateUpdated(function ($state, $record) {
                            // Guardar los atributos en la tabla attribute_variants
                            $record->attributes()->delete(); // Eliminar atributos existentes
                            foreach ($state as $key => $value) {
                                // Buscar o crear el atributo en la tabla attributes
                                $attribute = Attribute::firstOrCreate(['key' => $key]);
                                // Crear el registro en attribute_variants
                                $record->attributes()->create([
                                    'attribute_id' => $attribute->id,
                                    'value' => $value,
                                ]);
                            }
                        })
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('product.name')
                    ->label('Producto')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('attributes')
                    ->label('Atributos')
                    ->formatStateUsing(function ($record) { // obtiene todos los atributos y valor de la colección "attributes"
                        return $record->attributes
                            ->map(fn ($attributeVariant) => $attributeVariant->attribute->key . ': ' . $attributeVariant->value)
                            ->join(', ');
                    }),
                TextColumn::make('stock')
                    ->label('Inventario')
            ])
            ->filters([
                //
            ])
            ->actions([
                EditAction::make()->slideOver(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProductVariants::route('/'),
            'edit' => Pages\EditProductVariant::route('/{record}/edit'),
        ];
    }
}