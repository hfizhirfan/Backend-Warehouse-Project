<?php

namespace App\Filament\Resources\OrderDumps;

use App\Filament\Resources\OrderDumps\Pages\CreateOrderDump;
use App\Filament\Resources\OrderDumps\Pages\EditOrderDump;
use App\Filament\Resources\OrderDumps\Pages\ListOrderDumps;
use App\Filament\Resources\OrderDumps\Schemas\OrderDumpForm;
use App\Filament\Resources\OrderDumps\Tables\OrderDumpsTable;
use App\Models\OrderDump;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class OrderDumpResource extends Resource
{
    protected static ?string $model = OrderDump::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return OrderDumpForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return OrderDumpsTable::configure($table);
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
            'index' => ListOrderDumps::route('/'),
            'create' => CreateOrderDump::route('/create'),
            'edit' => EditOrderDump::route('/{record}/edit'),
        ];
    }
}
