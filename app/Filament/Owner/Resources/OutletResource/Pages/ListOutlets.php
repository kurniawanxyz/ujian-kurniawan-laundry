<?php

namespace App\Filament\Owner\Resources\OutletResource\Pages;

use App\Filament\Owner\Resources\OutletResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListOutlets extends ListRecords
{
    protected static string $resource = OutletResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
