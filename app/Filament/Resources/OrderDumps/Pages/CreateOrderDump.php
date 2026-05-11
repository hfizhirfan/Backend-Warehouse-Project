<?php

namespace App\Filament\Resources\OrderDumps\Pages;

use App\Filament\Resources\OrderDumps\OrderDumpResource;
use Filament\Resources\Pages\CreateRecord;

class CreateOrderDump extends CreateRecord
{
    protected static string $resource = OrderDumpResource::class;
}
