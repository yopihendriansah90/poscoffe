<?php

namespace App\Filament\Resources\Orders\Tables;

use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class OrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('row_index')
                    ->label('No')
                    ->rowIndex(),
                TextColumn::make('code')
                    ->label('Kode')
                    ->searchable()
                    ->sortable()
                    ->weight('semibold'),
                TextColumn::make('ordered_at')
                    ->label('Waktu')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
                TextColumn::make('order_type')
                    ->label('Jenis')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'dine_in' => 'Dine In',
                        'take_away' => 'Take Away',
                        default => ucfirst(str_replace('_', ' ', $state)),
                    })
                    ->badge(),
                TextColumn::make('diningTable.name')
                    ->label('Meja')
                    ->placeholder('-')
                    ->searchable(),
                TextColumn::make('payment.method')
                    ->label('Pembayaran')
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'cash' => 'Tunai',
                        'card' => 'Kartu',
                        'qris' => 'QRIS',
                        default => '-',
                    })
                    ->badge(),
                TextColumn::make('items_count')
                    ->label('Item')
                    ->counts('items')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'completed' => 'Selesai',
                        'pending' => 'Menunggu',
                        'open' => 'Terbuka',
                        'cancelled' => 'Dibatalkan',
                        default => ucfirst($state),
                    })
                    ->badge(),
                TextColumn::make('total')
                    ->label('Total')
                    ->money('IDR', locale: 'id', decimalPlaces: 0)
                    ->sortable(),
                TextColumn::make('user.name')
                    ->label('Kasir')
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('order_type')
                    ->label('Jenis pesanan')
                    ->options([
                        'dine_in' => 'Dine In',
                        'take_away' => 'Take Away',
                    ]),
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'completed' => 'Selesai',
                        'pending' => 'Menunggu',
                        'open' => 'Terbuka',
                        'cancelled' => 'Dibatalkan',
                    ]),
                SelectFilter::make('payment_method')
                    ->label('Metode pembayaran')
                    ->options([
                        'qris' => 'QRIS',
                        'cash' => 'Tunai',
                        'card' => 'Kartu',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if (blank($data['value'] ?? null)) {
                            return $query;
                        }

                        return $query->whereHas('payment', fn (Builder $query) => $query->where('method', $data['value']));
                    }),
            ])
            ->defaultSort('ordered_at', 'desc')
            ->recordActions([
                ViewAction::make()
                    ->label('Detail'),
            ]);
    }
}
