<?php

namespace App\Filament\Resources\Products\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;

class ProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('row_index')
                    ->label('No')
                    ->rowIndex(),
                ImageColumn::make('image_path')
                    ->label('Gambar')
                    ->getStateUsing(function ($record): ?string {
                        if (! $record->image_path) {
                            return null;
                        }

                        if (str_starts_with($record->image_path, 'http')) {
                            return $record->image_path;
                        }

                        if (Storage::disk('public')->exists($record->image_path)) {
                            return Storage::disk('public')->url($record->image_path);
                        }

                        return asset($record->image_path);
                    })
                    ->square(),
                TextColumn::make('name')
                    ->label('Produk')
                    ->searchable()
                    ->sortable()
                    ->weight('semibold'),
                TextColumn::make('category.name')
                    ->label('Kategori')
                    ->searchable()
                    ->sortable()
                    ->badge(),
                TextColumn::make('slug')
                    ->label('Slug')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('type')
                    ->label('Tipe')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'food' => 'Food',
                        'drink' => 'Drink',
                        default => ucfirst($state),
                    })
                    ->badge()
                    ->searchable(),
                TextColumn::make('price')
                    ->label('Harga')
                    ->money('IDR', locale: 'id', decimalPlaces: 0)
                    ->sortable(),
                TextColumn::make('stock_status')
                    ->label('Stok')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'available' => 'Tersedia',
                        'limited' => 'Terbatas',
                        'sold_out' => 'Habis',
                        default => ucfirst($state),
                    })
                    ->badge()
                    ->searchable(),
                IconColumn::make('is_discountable')
                    ->label('Diskon')
                    ->boolean(),
                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
                TextColumn::make('sort_order')
                    ->label('Urutan')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Diperbarui')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('category')
                    ->label('Kategori')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('type')
                    ->label('Tipe')
                    ->options([
                        'food' => 'Food',
                        'drink' => 'Drink',
                    ]),
                SelectFilter::make('stock_status')
                    ->label('Status stok')
                    ->options([
                        'available' => 'Tersedia',
                        'limited' => 'Terbatas',
                        'sold_out' => 'Habis',
                    ]),
                TernaryFilter::make('is_discountable')
                    ->label('Bisa diskon'),
                TernaryFilter::make('is_active')
                    ->label('Aktif di kasir'),
            ])
            ->defaultSort('sort_order')
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
