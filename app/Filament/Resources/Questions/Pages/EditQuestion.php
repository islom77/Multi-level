<?php

namespace App\Filament\Resources\Questions\Pages;

use App\Filament\Resources\Questions\QuestionResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditQuestion extends EditRecord
{
    protected static string $resource = QuestionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // QuestionData ma'lumotlarini formaga yuklash
        if ($this->record->questionData) {
            $data['questionData'] = [
                'text' => $this->record->questionData->text,
                'audio' => $this->record->questionData->audio,
            ];
        }

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
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

    protected function afterSave(): void
    {
        // Question saqlangandan keyin QuestionData ni yangilaymiz yoki yaratamiz
        if (!empty($this->questionDataFields)) {
            $this->record->questionData()->updateOrCreate(
                ['question_id' => $this->record->id],
                [
                    'text' => $this->questionDataFields['text'] ?? null,
                    'audio' => $this->questionDataFields['audio'] ?? null,
                ]
            );
        }
    }
}
