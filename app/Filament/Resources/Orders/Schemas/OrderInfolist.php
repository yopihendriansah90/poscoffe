<?php

namespace App\Filament\Resources\Orders\Schemas;

use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class OrderInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make([
                    'lg' => 3,
                ])
                    ->columnSpanFull()
                    ->schema([
                        Section::make('Ringkasan Transaksi')
                            ->schema([
                                TextEntry::make('code')
                                    ->label('Kode transaksi')
                                    ->weight('semibold'),
                                TextEntry::make('ordered_at')
                                    ->label('Waktu transaksi')
                                    ->dateTime('d M Y H:i'),
                                TextEntry::make('user.name')
                                    ->label('Kasir')
                                    ->placeholder('-'),
                                TextEntry::make('order_type')
                                    ->label('Jenis pesanan')
                                    ->formatStateUsing(fn (string $state): string => match ($state) {
                                        'dine_in' => 'Dine In',
                                        'take_away' => 'Take Away',
                                        default => ucfirst(str_replace('_', ' ', $state)),
                                    })
                                    ->badge(),
                                TextEntry::make('diningTable.name')
                                    ->label('Meja')
                                    ->placeholder('-'),
                                TextEntry::make('status')
                                    ->label('Status')
                                    ->formatStateUsing(fn (string $state): string => match ($state) {
                                        'completed' => 'Selesai',
                                        'pending' => 'Menunggu',
                                        'open' => 'Terbuka',
                                        'cancelled' => 'Dibatalkan',
                                        default => ucfirst($state),
                                    })
                                    ->badge(),
                            ])
                            ->columns([
                                'md' => 2,
                            ])
                            ->columnSpan([
                                'lg' => 2,
                            ]),
                        Section::make('Pembayaran')
                            ->schema([
                                TextEntry::make('payment.method')
                                    ->label('Metode')
                                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                                        'cash' => 'Tunai',
                                        'card' => 'Kartu',
                                        'qris' => 'QRIS',
                                        default => '-',
                                    })
                                    ->badge(),
                                TextEntry::make('payment.status')
                                    ->label('Status bayar')
                                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                                        'paid' => 'Lunas',
                                        'pending' => 'Menunggu',
                                        'failed' => 'Gagal',
                                        default => '-',
                                    })
                                    ->badge(),
                                TextEntry::make('payment.reference')
                                    ->label('Referensi')
                                    ->placeholder('-'),
                                TextEntry::make('payment.amount_paid')
                                    ->label('Uang diterima')
                                    ->money('IDR', locale: 'id', decimalPlaces: 0),
                                TextEntry::make('payment.change_amount')
                                    ->label('Kembalian')
                                    ->money('IDR', locale: 'id', decimalPlaces: 0),
                            ])
                            ->columnSpan([
                                'lg' => 1,
                            ]),
                    ]),
                Section::make('Item Pesanan')
                    ->schema([
                        RepeatableEntry::make('items')
                            ->label('')
                            ->schema([
                                TextEntry::make('product_name')
                                    ->label('Produk')
                                    ->weight('semibold'),
                                TextEntry::make('quantity')
                                    ->label('Qty'),
                                TextEntry::make('unit_price')
                                    ->label('Harga')
                                    ->money('IDR', locale: 'id', decimalPlaces: 0),
                                TextEntry::make('line_total')
                                    ->label('Subtotal item')
                                    ->money('IDR', locale: 'id', decimalPlaces: 0),
                                TextEntry::make('note')
                                    ->label('Catatan')
                                    ->placeholder('-')
                                    ->columnSpanFull(),
                            ])
                            ->columns([
                                'md' => 4,
                            ]),
                    ])
                    ->columnSpanFull(),
                Section::make('Ringkasan Biaya')
                    ->schema([
                        TextEntry::make('subtotal')
                            ->label('Subtotal')
                            ->money('IDR', locale: 'id', decimalPlaces: 0),
                        TextEntry::make('tax_rate')
                            ->label('Pajak')
                            ->suffix('%'),
                        TextEntry::make('tax_amount')
                            ->label('Nominal pajak')
                            ->money('IDR', locale: 'id', decimalPlaces: 0),
                        TextEntry::make('discount_name')
                            ->label('Promo')
                            ->placeholder('-'),
                        TextEntry::make('discount_amount')
                            ->label('Diskon')
                            ->money('IDR', locale: 'id', decimalPlaces: 0),
                        TextEntry::make('total')
                            ->label('Total')
                            ->money('IDR', locale: 'id', decimalPlaces: 0)
                            ->weight('bold'),
                    ])
                    ->columns([
                        'md' => 3,
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
