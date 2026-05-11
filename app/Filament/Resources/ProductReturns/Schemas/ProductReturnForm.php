<?php

namespace App\Filament\Resources\ProductReturns\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class ProductReturnForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('waybill')
                    ->required(),
                TextInput::make('product_id')
                    ->required()
                    ->numeric(),
                TextInput::make('quantity')
                    ->required()
                    ->numeric(),
                TextInput::make('condition')
                    ->required(),
                TextInput::make('inventory_status')
                    ->required(),
                Textarea::make('remark')
                    ->columnSpanFull(),
            ]);
    }
}
