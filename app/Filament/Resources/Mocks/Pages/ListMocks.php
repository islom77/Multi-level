<?php

namespace App\Filament\Resources\Mocks\Pages;

use App\Filament\Resources\Mocks\MockResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListMocks extends ListRecords
{
    protected static string $resource = MockResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
