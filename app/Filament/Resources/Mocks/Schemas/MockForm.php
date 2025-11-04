<?php

namespace App\Filament\Resources\Mocks\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;

class MockForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')->required(),
                TextInput::make('description'),
            ]);
    }
}
