<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\RawJs;
use Illuminate\Support\Str;

class ProductForm
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
                        Section::make('Informasi Produk')
                            ->schema([
                                TextInput::make('name')
                                    ->label('Nama produk')
                                    ->placeholder('Contoh: Beef Crowich')
                                    ->required()
                                    ->maxLength(255)
                                    ->validationAttribute('nama produk')
                                    ->validationMessages([
                                        'required' => 'Nama produk perlu diisi.',
                                        'max' => 'Nama produk terlalu panjang. Maksimal 255 karakter.',
                                    ])
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state ?? '')))
                                    ->columnSpanFull(),
                                TextInput::make('slug')
                                    ->label('Slug')
                                    ->required()
                                    ->readOnly()
                                    ->maxLength(255)
                                    ->unique(ignoreRecord: true)
                                    ->helperText('Slug dibuat otomatis dari nama produk dan tidak bisa diubah manual.')
                                    ->validationAttribute('slug produk')
                                    ->validationMessages([
                                        'required' => 'Slug produk perlu diisi.',
                                        'unique' => 'Slug ini sudah dipakai produk lain. Gunakan slug yang berbeda.',
                                        'max' => 'Slug produk terlalu panjang. Maksimal 255 karakter.',
                                    ]),
                                TextInput::make('price')
                                    ->label('Harga')
                                    ->required()
                                    ->mask(RawJs::make('$money($input, \',\', \'.\', 0)'))
                                    ->stripCharacters(['.', ','])
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->minValue(0)
                                    ->validationAttribute('harga')
                                    ->validationMessages([
                                        'required' => 'Harga produk perlu diisi.',
                                        'numeric' => 'Harga hanya boleh berisi angka. Contoh: 55.000',
                                        'min' => 'Harga tidak boleh kurang dari Rp 0.',
                                    ]),
                                Select::make('category_id')
                                    ->label('Kategori')
                                    ->relationship('category', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->validationAttribute('kategori produk')
                                    ->validationMessages([
                                        'required' => 'Pilih kategori untuk produk ini.',
                                        'exists' => 'Kategori yang dipilih tidak tersedia. Silakan pilih kategori lain.',
                                    ]),
                                Select::make('type')
                                    ->label('Tipe')
                                    ->options([
                                        'food' => 'Food',
                                        'drink' => 'Drink',
                                    ])
                                    ->native(false)
                                    ->required()
                                    ->default('food')
                                    ->validationAttribute('tipe produk')
                                    ->validationMessages([
                                        'required' => 'Pilih tipe produk.',
                                        'in' => 'Tipe produk tidak valid. Pilih Food atau Drink.',
                                    ]),
                            ])
                            ->columns([
                                'md' => 2,
                            ])
                            ->columnSpan([
                                'lg' => 2,
                            ]),
                        Section::make('Tampilan & Status')
                            ->schema([
                                FileUpload::make('image_path')
                                    ->label('Gambar produk')
                                    ->image()
                                    ->disk('public')
                                    ->directory('product-images')
                                    ->visibility('public')
                                    ->imageEditor()
                                    ->helperText('Ukuran ideal 800 x 600 px dengan rasio 4:3. Gunakan JPG/PNG/WebP yang jelas dan tidak terlalu gelap.'),
                                Select::make('stock_status')
                                    ->label('Status stok')
                                    ->options([
                                        'available' => 'Tersedia',
                                        'limited' => 'Terbatas',
                                        'sold_out' => 'Habis',
                                    ])
                                    ->native(false)
                                    ->required()
                                    ->default('available')
                                    ->validationAttribute('status stok')
                                    ->validationMessages([
                                        'required' => 'Pilih status stok produk.',
                                        'in' => 'Status stok tidak valid. Pilih status yang tersedia.',
                                    ]),
                                TextInput::make('sort_order')
                                    ->label('Urutan tampil')
                                    ->required()
                                    ->numeric()
                                    ->default(0)
                                    ->minValue(0)
                                    ->validationAttribute('urutan tampil')
                                    ->validationMessages([
                                        'required' => 'Urutan tampil perlu diisi.',
                                        'numeric' => 'Urutan tampil hanya boleh berisi angka.',
                                        'min' => 'Urutan tampil tidak boleh kurang dari 0.',
                                    ]),
                                Toggle::make('is_discountable')
                                    ->label('Bisa diskon')
                                    ->default(true)
                                    ->required()
                                    ->validationAttribute('status diskon')
                                    ->validationMessages([
                                        'required' => 'Tentukan apakah produk ini bisa mendapatkan diskon.',
                                    ]),
                                Toggle::make('is_active')
                                    ->label('Aktif di kasir')
                                    ->default(true)
                                    ->required()
                                    ->validationAttribute('status aktif produk')
                                    ->validationMessages([
                                        'required' => 'Tentukan apakah produk ini aktif di kasir.',
                                    ]),
                            ])
                            ->columnSpan([
                                'lg' => 1,
                            ]),
                    ]),
            ]);
    }
}
