<?php

namespace App\Filament\Resources\OrderResource\Pages;

use Filament\Actions;
use App\Helpers\FormatMoney;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components;
use Filament\Resources\Pages\ViewRecord;
use App\Filament\Resources\OrderResource;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\RepeatableEntry;

class ViewOrder extends ViewRecord
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
            Actions\Action::make('print')
                ->label('Imprimir')
                ->icon('heroicon-o-printer')
                /* ->url(fn () => route('orders.print', $this->record)) */
                ->openUrlInNewTab(),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Información de la Orden')
                    ->schema([
                        TextEntry::make('id')
                            ->label('ID de Orden'),
                            
                        TextEntry::make('user.name')
                            ->label('Cliente'),
                            
                        TextEntry::make('created_at')
                            ->label('Fecha y Hora')
                            ->formatStateUsing(function ($state) {
                                $date = $state->timezone('America/Hermosillo');
                                $months = [
                                    1 => 'enero', 2 => 'febrero', 3 => 'marzo', 4 => 'abril',
                                    5 => 'mayo', 6 => 'junio', 7 => 'julio', 8 => 'agosto',
                                    9 => 'septiembre', 10 => 'octubre', 11 => 'noviembre', 12 => 'diciembre'
                                ];
                                return $date->format('d').' de '.$months[$date->month].' del '.$date->format('Y h:i A');
                            })
                            ->tooltip(function ($state) {
                                \Carbon\Carbon::setLocale('es');
                                return "Hace ".$state->timezone('America/Hermosillo')->diffForHumans();
                            }),
                            
                        TextEntry::make('status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'completed' => 'success',
                                'pending' => 'warning',
                                'cancelled' => 'danger',
                                default => 'gray',
                            }),
                    ])->columns(2),
                    
                Section::make('Detalles de Pago')
                    ->schema([
                        TextEntry::make('amount_subtotal')
                            ->label('Subtotal')
                            ->formatStateUsing(fn ($state) => FormatMoney::format($state)),
                            
                        TextEntry::make('amount_tax')
                            ->label('Impuestos')
                            ->formatStateUsing(fn ($state) => FormatMoney::format($state)),
                            
                        TextEntry::make('amount_shipping')
                            ->label('Envío')
                            ->formatStateUsing(fn ($state) => FormatMoney::format($state)),
                            
                        TextEntry::make('amount_total')
                            ->label('Total')
                            ->formatStateUsing(fn ($state) => FormatMoney::format($state))
                            ->weight('bold'),
                    ])->columns(2),
                    
                Section::make('Productos')
                    ->schema([
                        RepeatableEntry::make('items')
                            ->schema([
                                ImageEntry::make('product.featured_image')
                                    ->label('Imagen')
                                    ->width('80px')
                                    ->height('80px'),
                                    
                                TextEntry::make('product.name')
                                    ->label('Producto')
                                    ->weight('bold'),
                                    
                                TextEntry::make('quantity')
                                    ->label('Cantidad'),
                                    
                                TextEntry::make('price')
                                    ->label('Precio Unitario')
                                    ->formatStateUsing(fn ($state) => FormatMoney::format($state)),
                                    
                                TextEntry::make('total')
                                    ->label('Total')
                                    ->formatStateUsing(fn ($state, $record) => FormatMoney::format($record->price * $record->quantity)),
                            ])
                            ->columns(5)
                            ->columnSpanFull(),
                        Section::make('Dirección de Envío')
                            ->schema([
                                TextEntry::make('shipping_address.name')
                                    ->label('Nombre')
                                    ->default(fn ($record) => $record->shipping_address['name'] ?? 'N/A'),
                                    
                                TextEntry::make('shipping_address.line1')
                                    ->label('Dirección Línea 1')
                                    ->default(fn ($record) => $record->shipping_address['line1'] ?? 'N/A'),
                                    
                                TextEntry::make('shipping_address.line2')
                                    ->label('Dirección Línea 2')
                                    ->default(fn ($record) => $record->shipping_address['line2'] ?? ''),
                                    
                                TextEntry::make('shipping_address.city')
                                    ->label('Ciudad')
                                    ->default(fn ($record) => $record->shipping_address['city'] ?? 'N/A'),
                                    
                                TextEntry::make('shipping_address.state')
                                    ->label('Estado')
                                    ->default(fn ($record) => $record->shipping_address['state'] ?? 'N/A'),
                                    
                                TextEntry::make('shipping_address.postal_code')
                                    ->label('Código Postal')
                                    ->default(fn ($record) => $record->shipping_address['postal_code'] ?? 'N/A'),
                                    
                                TextEntry::make('shipping_address.country')
                                    ->label('País')
                                    ->default(fn ($record) => $this->getCountryName($record->shipping_address['country'] ?? 'MX')),
                            ])
                        ->columns(2),
                    Section::make('Dirección de Facturación')
                        ->schema([
                            TextEntry::make('billing_address.name')
                                ->label('Nombre')
                                ->default(fn ($record) => $record->billing_address['name'] ?? 'N/A'),
                                
                            TextEntry::make('billing_address.line1')
                                ->label('Dirección Línea 1')
                                ->default(fn ($record) => $record->billing_address['line1'] ?? 'N/A'),
                                
                            TextEntry::make('billing_address.line2')
                                ->label('Dirección Línea 2')
                                ->default(fn ($record) => $record->billing_address['line2'] ?? ''),
                                
                            TextEntry::make('billing_address.city')
                                ->label('Ciudad')
                                ->default(fn ($record) => $record->billing_address['city'] ?? 'N/A'),
                                
                            TextEntry::make('billing_address.state')
                                ->label('Estado')
                                ->default(fn ($record) => $record->billing_address['state'] ?? 'N/A'),
                                
                            TextEntry::make('billing_address.postal_code')
                                ->label('Código Postal')
                                ->default(fn ($record) => $record->billing_address['postal_code'] ?? 'N/A'),
                                
                            TextEntry::make('billing_address.country')
                                ->label('País')
                                ->default(fn ($record) => $this->getCountryName($record->billing_address['country'] ?? 'MX')),
                        ])
                    ->columns(2),
                ]),
            ]);
    }
}