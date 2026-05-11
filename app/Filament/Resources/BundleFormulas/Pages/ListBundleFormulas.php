<?php

namespace App\Filament\Resources\BundleFormulas\Pages;

use App\Filament\Resources\BundleFormulas\BundleFormulaResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListBundleFormulas extends ListRecords
{
    protected static string $resource = BundleFormulaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
