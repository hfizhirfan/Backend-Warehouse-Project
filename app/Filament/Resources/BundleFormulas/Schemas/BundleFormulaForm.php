<?php

namespace App\Filament\Resources\BundleFormulas\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class BundleFormulaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('parent_sku')
                    ->required(),
                TextInput::make('child_sku')
                    ->required(),
                TextInput::make('multiplier')
                    ->required()
                    ->numeric(),
            ]);
    }
}
