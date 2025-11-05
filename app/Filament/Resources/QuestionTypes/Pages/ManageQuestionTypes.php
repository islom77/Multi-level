<?php

namespace App\Filament\Resources\QuestionTypes\Pages;

use App\Filament\Resources\QuestionTypes\QuestionTypeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageQuestionTypes extends ManageRecords
{
    protected static string $resource = QuestionTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
