<?php

namespace App\Filament\Resources\Mocks\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\CreatedAtColumn;
use Filament\Tables\Columns\UpdatedAtColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use App\Models\Mock;

class MocksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('description'),
                // CreatedAtColumn::make(),
                // UpdatedAtColumn::make(),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make(),
                Action::make('viewPdf')
                    ->label('PDF ko\'rish')
                    ->icon('heroicon-o-document-text')
                    ->color('info')
                    ->url(fn (Mock $record): string => route('mocks.pdf', $record))
                    ->openUrlInNewTab(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
