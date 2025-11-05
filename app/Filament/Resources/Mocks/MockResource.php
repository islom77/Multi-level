<?php

namespace App\Filament\Resources\Mocks;

use App\Filament\Resources\Mocks\Pages\CreateMock;
use App\Filament\Resources\Mocks\Pages\EditMock;
use App\Filament\Resources\Mocks\Pages\ListMocks;
use App\Filament\Resources\Mocks\Schemas\MockForm;
use App\Filament\Resources\Mocks\Tables\MocksTable;
use App\Models\Mock;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MockResource extends Resource
{
    protected static ?string $model = Mock::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return MockForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MocksTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMocks::route('/'),
            'create' => CreateMock::route('/create'),
            'edit' => EditMock::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
