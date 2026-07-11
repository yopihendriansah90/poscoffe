<?php

namespace App\Filament\Resources\Categories\Schemas;

use App\Models\Category;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class CategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Kategori')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama kategori')
                            ->placeholder('Contoh: Pastry')
                            ->required()
                            ->maxLength(255)
                            ->validationAttribute('nama kategori')
                            ->validationMessages([
                                'required' => 'Nama kategori perlu diisi.',
                                'max' => 'Nama kategori terlalu panjang. Maksimal 255 karakter.',
                            ])
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state ?? '')))
                            ->columnSpan([
                                'md' => 2,
                                'xl' => 2,
                            ]),
                        TextInput::make('slug')
                            ->label('Slug')
                            ->required()
                            ->readOnly()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->helperText('Slug dibuat otomatis dari nama kategori dan tidak bisa diubah manual.')
                            ->validationAttribute('slug kategori')
                            ->validationMessages([
                                'required' => 'Slug kategori perlu diisi.',
                                'unique' => 'Slug ini sudah dipakai kategori lain. Gunakan slug yang berbeda.',
                                'max' => 'Slug kategori terlalu panjang. Maksimal 255 karakter.',
                            ])
                            ->columnSpan([
                                'md' => 2,
                                'xl' => 2,
                            ]),
                        Select::make('icon')
                            ->label('Ikon Material Symbols')
                            ->options(Category::materialIconOptionsWithPreview())
                            ->searchable()
                            ->native(false)
                            ->allowHtml()
                            ->default('category')
                            ->required()
                            ->validationAttribute('ikon kategori')
                            ->validationMessages([
                                'required' => 'Pilih satu ikon untuk kategori ini.',
                            ])
                            ->helperText('Dipakai untuk ikon kategori di halaman kasir.')
                            ->columnSpan([
                                'md' => 2,
                                'xl' => 2,
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
                            ])
                            ->columnSpan([
                                'md' => 1,
                                'xl' => 1,
                            ]),
                        Toggle::make('is_active')
                            ->label('Aktif')
                            ->default(true)
                            ->required()
                            ->validationAttribute('status aktif')
                            ->validationMessages([
                                'required' => 'Tentukan apakah kategori ini aktif atau tidak.',
                            ])
                            ->columnSpan([
                                'md' => 1,
                                'xl' => 1,
                            ]),
                    ])
                    ->columnSpanFull()
                    ->columns([
                        'md' => 2,
                        'xl' => 4,
                    ]),
            ]);
    }
}
