<?php

namespace App\Filament\Resources\DiningTables\Tables;

use App\Filament\Resources\DiningTables\Schemas\DiningTableForm;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Filament\Support\Enums\Width;

class DiningTablesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Meja')
                    ->searchable()
                    ->sortable()
                    ->weight('semibold'),
                TextColumn::make('code')
                    ->label('Kode')
                    ->searchable()
                    ->badge(),
                TextColumn::make('capacity')
                    ->label('Kapasitas')
                    ->numeric()
                    ->suffix(' orang')
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'available' => 'Tersedia',
                        'occupied' => 'Terisi',
                        'reserved' => 'Reservasi',
                        'maintenance' => 'Perawatan',
                        default => ucfirst($state),
                    })
                    ->badge()
                    ->searchable(),
                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
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
                SelectFilter::make('status')
                    ->label('Status meja')
                    ->options([
                        'available' => 'Tersedia',
                        'occupied' => 'Terisi',
                        'reserved' => 'Reservasi',
                        'maintenance' => 'Perawatan',
                    ]),
                TernaryFilter::make('is_active')
                    ->label('Aktif di kasir'),
            ])
            ->defaultSort('code')
            ->recordActions([
                EditAction::make()
                    ->label('Ubah')
                    ->modalHeading('Ubah Meja')
                    ->modalWidth(Width::Large)
                    ->modalSubmitActionLabel('Simpan')
                    ->schema(DiningTableForm::components()),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
