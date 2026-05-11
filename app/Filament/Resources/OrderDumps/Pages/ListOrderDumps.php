<?php

namespace App\Filament\Resources\OrderDumps\Pages;

use App\Filament\Resources\OrderDumps\OrderDumpResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListOrderDumps extends ListRecords
{
    protected static string $resource = OrderDumpResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
