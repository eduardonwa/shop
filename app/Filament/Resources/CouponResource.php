<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Coupon;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\DateTimePicker;
use App\Filament\Resources\CouponResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\CouponResource\RelationManagers;

class CouponResource extends Resource
{
    protected static ?string $model = Coupon::class;

    protected static ?string $navigationGroup = 'Tienda';

    public static function getModelLabel(): string
    {
        return 'Cupón';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Cupones';
    }

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        TextInput::make('code')
                            ->label('Código')
                            ->helperText('Este código lo ingresarán los clientes al pagar')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(32),
                        TextInput::make('title')
                            ->label('Nombre')
                            ->required()
                            ->maxLength(255),
                        Select::make('discount_type')
                            ->label('Tipo')
                            ->options([
                                'percentage' => 'Porcentaje',
                                'fixed' => 'Monto fijo',
                            ])
                            ->required()
                            ->helperText(
                                fn ($get) => $get('discount_type') === 'percentage' 
                                    ? 'Ejemplo: 20 = 20% de descuento' 
                                    : 'Ejemplo: 500 = $500 de descuento fijo'
                            )
                            ->reactive(),
                        TextInput::make('discount_value')
                            ->label('Valor de descuento')
                            ->numeric()
                            ->required()
                            ->rules(['min: 0'])
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, Set $set, $get) {
                                // si es porcentaje, asegurar que no sea mayor a 100
                                if ($get('discount_type') === 'percentage' && $state > 100) {
                                    $set('discount_value', 100);
                                }
                            })
                            ->afterStateHydrated(function ($component, $state, $get) {
                                // convertir centavos a pesos para montos fijos
                                if ($state && $get('discount_type') === 'fixed') {
                                    $valueInPesos = $state / 100;
                                    $component->state(number_format($valueInPesos, 2, '.', ''));                                    
                                }
                            })
                            ->dehydrateStateUsing(function ($state, $get) {
                                // convertir a centavos si es monto fijo
                                return $get('discount_type') === 'fixed'
                                    ? (int) round($state * 100)
                                    : $state; 
                            })
                            ->helperText('No necesitas escribir signos'),
                        DateTimePicker::make('starts_at')
                            ->label('Fecha de inicio')
                            ->timezone('America/Hermosillo')
                            ->displayFormat('d-m-Y h:i A')
                            ->format('Y-m-d H:i:s')
                            ->native(false),
                        DateTimePicker::make('expires_at')
                            ->label('Fecha de expiración')
                            ->timezone('America/Hermosillo')
                            ->displayFormat('d-m-Y h:i A')
                            ->format('Y-m-d H:i:s')
                            ->native(false)
                            ->minDate(fn ($get) => $get('starts_at')),
                        Toggle::make('is_active')
                            ->label(fn (Get $get) => $get('is_active') ? 'Desactivar cupón' : 'Activar cupón')
                            ->live()
                            ->inline(true)
                            ->default(true),
                    ])->columns(2),

                    Section::make('Productos')
                        ->schema([
                            Select::make('products')
                                ->label('Productos que utilizan este cupón')
                                ->relationship('products', 'name')
                                ->multiple()
                                ->preload()
                                ->searchable()
                                ->optionsLimit(50)
                                ->maxItems(50),
                        ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->label('Código')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('discount_type')
                    ->label('Tipo')
                    ->sortable()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'fixed' => 'Monto fijo',
                        'percentage' => 'Porcentaje',
                        default => $state,
                    }),
                TextColumn::make('discount_value')
                    ->sortable()
                    ->label('Valor'),
                TextColumn::make('is_active')
                    ->label('Estado')
                    ->badge()
                    ->sortable()
                    ->formatStateUsing(fn (bool $state): string => $state ? 'Activo' : 'Inactivo')
                    ->color(fn (bool $state): string => $state ? 'success' : 'danger'),
                TextColumn::make('expires_at')
                    ->label('Expira en')
                    ->sortable()
                    ->formatStateUsing(fn ($record) => $record->remaining_time)
                    ->description(fn ($record) => $record->expires_at?->timezone('America/Hermosillo')->format('d/m/Y h:i A')),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCoupons::route('/'),
            'create' => Pages\CreateCoupon::route('/create'),
            'edit' => Pages\EditCoupon::route('/{record}/edit'),
        ];
    }
}
