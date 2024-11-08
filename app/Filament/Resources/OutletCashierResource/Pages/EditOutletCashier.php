<?php

namespace App\Filament\Resources\OutletCashierResource\Pages;

use App\Filament\Resources\OutletCashierResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditOutletCashier extends EditRecord
{
    protected static string $resource = OutletCashierResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
