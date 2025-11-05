<?php

namespace App\Filament\Resources\Questions\Pages;

use App\Filament\Resources\Questions\QuestionResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateQuestion extends CreateRecord
{
    protected static string $resource = QuestionResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // QuestionData ma'lumotlarini ajratib olamiz
        $questionDataFields = [];
        if (isset($data['questionData'])) {
            $questionDataFields = $data['questionData'];
            unset($data['questionData']);
        }

        // questionData ni keyinroq ishlatish uchun saqlaymiz
        $this->questionDataFields = $questionDataFields;

        return $data;
    }

    protected function afterCreate(): void
    {
        // Question yaratilgandan keyin QuestionData ni yaratamiz
        if (!empty($this->questionDataFields)) {
            $this->record->questionData()->create([
                'text' => $this->questionDataFields['text'] ?? null,
                'audio' => $this->questionDataFields['audio'] ?? null,
            ]);
        }
    }
}
