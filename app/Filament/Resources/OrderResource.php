<?php

namespace App\Filament\Resources;

use Money\Money;
use Filament\Forms;
use Filament\Tables;
use App\Models\Order;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\OrderResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\OrderResource\RelationManagers;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationGroup = 'Ventas';
    
    public static function getModelLabel(): string
    {
        return 'Orden';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Ordenes';
    }
    
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->sortable(),
                TextColumn::make('user.name')
                    ->label('Cliente'),
                TextColumn::make('amount_total')
                    ->label('Total')
                    ->formatStateUsing(function ($state) {
                        $value = $state instanceof Money 
                            ? $state->getAmount() / 100 
                            : $state / 100;
                        
                        return '$ ' . number_format($value, 2, '.', ',');
                    }),
                TextColumn::make('created_at')
                    ->label('Fecha')
                    ->sortable()
                    ->dateTime('d/m/Y h:i A', 'America/Hermosillo')
                    ->timezone('America/Hermosillo'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
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
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
            'view' => Pages\ViewOrder::route('/{record}')
        ];
    }
}
