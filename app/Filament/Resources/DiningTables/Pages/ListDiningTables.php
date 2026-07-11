<?php

namespace App\Filament\Resources\DiningTables\Pages;

use App\Filament\Resources\DiningTables\DiningTableResource;
use App\Filament\Resources\DiningTables\Schemas\DiningTableForm;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\Width;

class ListDiningTables extends ListRecords
{
    protected static string $resource = DiningTableResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Tambah Meja')
                ->modalHeading('Tambah Meja')
                ->modalWidth(Width::Large)
                ->modalSubmitActionLabel('Simpan')
                ->createAnother(false)
                ->schema(DiningTableForm::components()),
        ];
    }
}
