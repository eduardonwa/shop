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
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Tabs\Tab;
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

    protected static ?string $navigationGroup = 'Tienda';

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
                            ->required()
                            ->columnSpanFull(),
                        SpatieMediaLibraryFileUpload::make('images')
                            ->label('Imagenes')
                            ->maxSize(1500)
                            ->collection('images')
                            ->multiple()
                            ->maxFiles(3)
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
                        Tabs::make('Tabs')
                            ->tabs([
                                Tab::make('Información')
                                    ->schema([
                                        TextInput::make('name')
                                            ->label('Nombre'),
                                        Textarea::make('description')
                                            ->label('Descripción')
                                            ->rows(4),
                                        Grid::make(2)
                                            ->schema([
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
                                                    ->label('Publicar en tienda')
                                                    ->inline(false)
                                                    //->disabled(fn ($get) => $get('stock_status') === 'sold_out'),
                                            ])
                                    ]),
                                Tab::make('Inventario')
                                    ->schema([
                                        TextInput::make('total_product_stock')
                                            ->label('Unidades')
                                            ->numeric()
                                            ->disabled(fn ($get) => $get('has_variants'))
                                            ->reactive()
                                            ->formatStateUsing(function ($state, $record) {
                                                try {
                                                    return optional($record)->variants?->sum('total_variant_stock') ?? $state ?? 0;
                                                } catch (\Exception $e) {
                                                    return 0;
                                                }
                                            })
                                            ->afterStateUpdated(function ($state, $set, $livewire) {
                                                $lowStockThreshold = $livewire->record->low_stock_threshold ?? 5;
                                                $newStatus = $state <= 0 ? 'sold_out' :
                                                    ($state <= $lowStockThreshold ? 'low_stock' : 'in_stock');
                                                $set('stock_status', $newStatus);
                                            }),
                                        Select::make('stock_status')
                                            ->label('Estado de inventario')
                                            ->options([
                                                'in_stock' => 'Disponible',
                                                'low_stock' => 'Últimas unidades',
                                                'sold_out' => 'Agotado',
                                            ])
                                            ->required()
                                            ->reactive(),
                                        TextInput::make('low_stock_threshold')
                                            ->label('Umbral para bajo stock')
                                            ->numeric()
                                            ->minValue(1)
                                            ->default(5),
                                    ])
                            ]),
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
                    ->size(50)
                    ->extraImgAttributes([
                        'style' => 'border-radius: 0.5rem;'
                    ]),
                TextColumn::make('price')
                    ->label('Precio')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('name')
                    ->label('Nombre')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('variants_count')
                    ->label('Variaciones')
                    ->counts('variants')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('total_product_stock')
                    ->label('Inventario')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(function ($state, $record) {
                        // Si el producto tiene variantes, suma el stock desde la tabla pivote
                        if ($record->variants()->exists()) {
                            return $record->variants()->sum('product_variants.total_variant_stock');
                        }
                        // Si no tiene variantes, muestra el valor manual
                        return $state ?? 0;
                    }),
                TextColumn::make('published')
                    ->label('Estado')
                    ->badge()
                    ->sortable()
                    ->formatStateUsing(fn (bool $state): string => $state ? 'Activo' : 'Inactivo')
                    ->color(fn (bool $state): string => $state ? 'success' : 'danger'),
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
