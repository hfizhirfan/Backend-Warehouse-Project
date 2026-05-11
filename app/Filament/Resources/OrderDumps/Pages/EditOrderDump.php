<?php

namespace App\Filament\Resources\OrderDumps\Pages;

use App\Filament\Resources\OrderDumps\OrderDumpResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditOrderDump extends EditRecord
{
    protected static string $resource = OrderDumpResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
