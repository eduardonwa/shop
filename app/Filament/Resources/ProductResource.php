<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Product;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Forms\Components\TextInput\Mask;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\ImageColumn;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\ProductResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\ProductResource\RelationManagers;
use Filament\Support\RawJs;
use Money\Money;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name'),
                TextInput::make('description'),
                TextInput::make('price')
                    ->label('Precio')
                    ->mask(RawJs::make('$money($input'))
                    ->stripCharacters([',', '$'])
                    ->numeric()
                    ->required()
                    ->rules(['numeric', 'min:0'])
                    ->afterStateHydrated(function ($component, $state) {
                        // si $state es un objeto Money, extraer su valor numérico
                        if ($state instanceof \Money\Money) {
                            $state = $state->getAmount(); // obtiene el valor en centavos
                        }

                        // convertir el valor de centavos a un formato legible (por ejemplo de 4444 a 44.44)
                        $formattedPrice = number_format($state / 100, 2, '.', '');
                        $component->state($formattedPrice);
                    })
                    ->dehydrateStateUsing(function ($state) {
                        // convierte el valor formateado de vuelta a centavos para la base de datos
                        return (int) round($state * 100);
                    }),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image.path')
                    ->label('Featured Image')
                    ->size(50),
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('description')
                    ->searchable(),
                TextColumn::make('price')
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
            //
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
