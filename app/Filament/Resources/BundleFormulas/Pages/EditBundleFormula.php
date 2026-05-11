<?php

namespace App\Filament\Resources\BundleFormulas\Pages;

use App\Filament\Resources\BundleFormulas\BundleFormulaResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditBundleFormula extends EditRecord
{
    protected static string $resource = BundleFormulaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
