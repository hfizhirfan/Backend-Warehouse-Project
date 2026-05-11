<?php

namespace App\Filament\Resources\OrderDumps\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class OrderDumpForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('waybill')
                    ->required(),
                TextInput::make('platform')
                    ->required(),
                TextInput::make('store')
                    ->required(),
                DatePicker::make('order_date')
                    ->required(),
                TextInput::make('status')
                    ->required()
                    ->default('pending'),
            ]);
    }
}
