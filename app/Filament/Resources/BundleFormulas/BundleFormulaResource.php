<?php

namespace App\Filament\Resources\BundleFormulas;

use App\Filament\Resources\BundleFormulas\Pages\CreateBundleFormula;
use App\Filament\Resources\BundleFormulas\Pages\EditBundleFormula;
use App\Filament\Resources\BundleFormulas\Pages\ListBundleFormulas;
use App\Filament\Resources\BundleFormulas\Schemas\BundleFormulaForm;
use App\Filament\Resources\BundleFormulas\Tables\BundleFormulasTable;
use App\Models\BundleFormula;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class BundleFormulaResource extends Resource
{
    protected static ?string $model = BundleFormula::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return BundleFormulaForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BundleFormulasTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBundleFormulas::route('/'),
            'create' => CreateBundleFormula::route('/create'),
            'edit' => EditBundleFormula::route('/{record}/edit'),
        ];
    }
}
