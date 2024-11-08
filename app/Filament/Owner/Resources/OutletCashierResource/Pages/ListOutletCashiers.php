<?php

namespace App\Filament\Owner\Resources\OutletCashierResource\Pages;

use App\Filament\Owner\Resources\OutletCashierResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListOutletCashiers extends ListRecords
{
    protected static string $resource = OutletCashierResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}