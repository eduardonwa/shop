<?php

namespace App\Filament\Resources;

use Money\Money;
use Filament\Tables;
use App\Models\Product;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Support\RawJs;
use Filament\Resources\Resource;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use App\Filament\Resources\ProductResource\Pages;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use App\Filament\Resources\ProductResource\RelationManagers\VariantsRelationManager;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getModelLabel(): string
    {
        return 'Productos';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Productos';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(2)
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('featured_image')
                            ->label('Imagen destacada')
                            ->maxSize(3000)
                            ->collection('featured')
                            ->image()
                            ->columnSpanFull(),
                        SpatieMediaLibraryFileUpload::make('images')
                            ->label('Imagenes')
                            ->maxSize(1500)
                            ->collection('images')
                            ->multiple()
                            ->image()
                            ->extraAttributes(['class' => 'clase'])
                            ->columnSpanFull()
                            ->panelLayout('grid')
                    ])->columnSpan([
                        'default' => 1,
                        'sm' => 12,
                        'md' => 8,
                        'lg' => 5,
                    ]),
                Grid::make(1)
                    ->schema([
                        TextInput::make('name')
                            ->label('Nombre'),
                        Textarea::make('description')
                            ->label('Descripción')
                            ->rows(4),
                        TextInput::make('price')
                            ->label('Precio')
                            ->placeholder('1,000')
                            ->mask(RawJs::make('$money($input, ",")'))
                            ->stripCharacters([',', '$'])
                            ->numeric()
                            ->required()
                            ->rules(['min:0'])
                            ->afterStateHydrated(function ($component, $state) {
                                // Si $state es un objeto Money, extraer su valor numérico
                                if ($state instanceof \Money\Money) {
                                    $state = $state->getAmount(); // Obtiene el valor en centavos
                                }
                                // Convertir el valor de centavos a un formato legible (por ejemplo, 4444 a 44.44)
                                $valueInPesos = $state / 100;
                                // Verificar si el valor tiene decimales distintos de cero
                                if (fmod($valueInPesos, 1) == 0) {
                                    // Si no tiene decimales, formatear sin los dos ceros
                                    $formattedPrice = number_format($valueInPesos, 0, '.', ',');
                                } else {
                                    // Si tiene decimales, formatear con dos decimales
                                    $formattedPrice = number_format($valueInPesos, 2, '.', ',');
                                }
                                $component->state($formattedPrice);
                            })
                            ->dehydrateStateUsing(function ($state) {
                                // Eliminar comas y simbolos antes de convertirlo a centavos
                                $state = str_replace([',', '$'], '', $state);
                                // Convertir el valor formateado de vuelta a centavos para la base de datos
                                return (int) round($state * 100);
                            }),
                        Toggle::make('published')
                            ->label('Publicar')
                            ->inline(false),
                        TextInput::make('total_stock')
                            ->label('Inventario')
                            ->numeric(),
                ])->columnSpan([
                    'default' => 1,
                    'sm' => 12,
                    'md' => 8,
                    'lg' => 7,
                ]),
            ])->columns(12);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                SpatieMediaLibraryImageColumn::make('Imagen')
                    ->collection('featured')
                    ->size(50),
                TextColumn::make('price')
                    ->label('Precio')
                    ->searchable(),
                TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable(),
                TextColumn::make('variants_count')
                    ->label('Variaciones')
                    ->counts('variants'),
                TextColumn::make('total_stock')
                    ->label('Inventario'),
                TextColumn::make('description')
                    ->label('Descripción')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            VariantsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
