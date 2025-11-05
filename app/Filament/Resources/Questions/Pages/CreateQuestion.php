<?php

namespace App\Filament\Resources\Questions\Pages;

use App\Filament\Resources\Questions\QuestionResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use App\Models\QuestionType;

class CreateQuestion extends CreateRecord
{
    protected static string $resource = QuestionResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        // Parent question yaratish
        $parentQuestion = static::getModel()::create([
            'question_type_id' => $data['question_type_id'],
            'name' => $data['name'],
            'text' => $data['text'] ?? null,
            'order' => $data['order'] ?? null,
            'parent_id' => $data['parent_id'] ?? null,
            'true_option_id' => $data['true_option_id'] ?? null,
        ]);

        // Fill in the Blank uchun QuestionData va child questions
        $questionType = QuestionType::find($data['question_type_id']);

        if ($questionType && $questionType->name === 'Fill in the Blank') {
            // QuestionData yaratish
            if (isset($data['questionData']) && count($data['questionData']) > 0) {
                foreach ($data['questionData'] as $questionDataItem) {
                    $questionData = $parentQuestion->questionData()->create([
                        'text' => $questionDataItem['text'] ?? null,
                        'audio' => $questionDataItem['audio'] ?? null,
                    ]);

                    // Child questions yaratish
                    if (isset($data['children']) && count($data['children']) > 0) {
                        foreach ($data['children'] as $childData) {
                            $childQuestion = $parentQuestion->children()->create([
                                'question_type_id' => $data['question_type_id'],
                                'name' => $childData['name'],
                                'text' => $childData['text'] ?? null,
                                'order' => $childData['order'] ?? null,
                            ]);

                            // KeyWords yaratish
                            if (isset($childData['keyWords']) && count($childData['keyWords']) > 0) {
                                foreach ($childData['keyWords'] as $keyWordData) {
                                    $childQuestion->keyWords()->create([
                                        'question_data_id' => $questionData->id,
                                        'word' => $keyWordData['word'],
                                    ]);
                                }
                            }
                        }
                    }
                }
            }
        } else {
            // Boshqa savol turlari uchun oddiy child questions
            if (isset($data['children']) && count($data['children']) > 0) {
                foreach ($data['children'] as $childData) {
                    $parentQuestion->children()->create([
                        'question_type_id' => $data['question_type_id'],
                        'name' => $childData['name'],
                        'text' => $childData['text'] ?? null,
                        'order' => $childData['order'] ?? null,
                        'true_option_id' => $childData['true_option_id'] ?? null,
                    ]);
                }
            }

            // Options yaratish
            if (isset($data['options']) && count($data['options']) > 0) {
                foreach ($data['options'] as $optionData) {
                    $parentQuestion->options()->create([
                        'title' => $optionData['title'],
                        'text' => $optionData['text'] ?? null,
                        'order' => $optionData['order'] ?? null,
                    ]);
                }
            }
        }

        return $parentQuestion;
    }
}
