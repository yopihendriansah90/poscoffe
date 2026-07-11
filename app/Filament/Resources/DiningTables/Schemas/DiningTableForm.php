<?php

namespace App\Filament\Resources\DiningTables\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class DiningTableForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components(static::components());
    }

    public static function components(): array
    {
        return [
            Section::make('Informasi Meja')
                ->schema([
                    TextInput::make('name')
                        ->label('Nama meja')
                        ->placeholder('Contoh: Meja 01')
                        ->required()
                        ->maxLength(255)
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn (Set $set, ?string $state) => $set('code', Str::upper(Str::slug($state ?? '', '-'))))
                        ->validationAttribute('nama meja')
                        ->validationMessages([
                            'required' => 'Nama meja perlu diisi.',
                            'max' => 'Nama meja terlalu panjang. Maksimal 255 karakter.',
                        ]),
                    TextInput::make('code')
                        ->label('Kode meja')
                        ->required()
                        ->readOnly()
                        ->maxLength(255)
                        ->unique(ignoreRecord: true)
                        ->helperText('Kode dibuat otomatis dari nama meja dan tidak bisa diubah manual.')
                        ->validationAttribute('kode meja')
                        ->validationMessages([
                            'required' => 'Kode meja perlu diisi.',
                            'unique' => 'Kode ini sudah dipakai meja lain. Gunakan nama meja yang berbeda.',
                            'max' => 'Kode meja terlalu panjang. Maksimal 255 karakter.',
                        ]),
                    TextInput::make('capacity')
                        ->label('Kapasitas')
                        ->required()
                        ->numeric()
                        ->default(2)
                        ->minValue(1)
                        ->maxValue(99)
                        ->validationAttribute('kapasitas meja')
                        ->validationMessages([
                            'required' => 'Kapasitas meja perlu diisi.',
                            'numeric' => 'Kapasitas meja hanya boleh berisi angka.',
                            'min' => 'Kapasitas meja minimal 1 orang.',
                            'max' => 'Kapasitas meja maksimal 99 orang.',
                        ]),
                    Select::make('status')
                        ->label('Status meja')
                        ->options([
                            'available' => 'Tersedia',
                            'occupied' => 'Terisi',
                            'reserved' => 'Reservasi',
                            'maintenance' => 'Perawatan',
                        ])
                        ->native(false)
                        ->required()
                        ->default('available')
                        ->validationAttribute('status meja')
                        ->validationMessages([
                            'required' => 'Pilih status meja.',
                            'in' => 'Status meja tidak valid. Pilih status yang tersedia.',
                        ]),
                    Toggle::make('is_active')
                        ->label('Aktif di kasir')
                        ->default(true)
                        ->required()
                        ->validationAttribute('status aktif meja')
                        ->validationMessages([
                            'required' => 'Tentukan apakah meja ini aktif di kasir.',
                        ]),
                ])
                ->columnSpanFull()
                ->columns(1),
        ];
    }
}
