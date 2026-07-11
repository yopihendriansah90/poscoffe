<?php

namespace App\Filament\Resources\DiningTables\Pages;

use App\Filament\Resources\DiningTables\DiningTableResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Enums\Width;

class CreateDiningTable extends CreateRecord
{
    protected static string $resource = DiningTableResource::class;

    protected Width|string|null $maxContentWidth = Width::ScreenExtraLarge;
}
