<?php

namespace App\Filament\Resources\Mocks\Pages;

use App\Filament\Resources\Mocks\MockResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;
use App\Models\MockSkill;
use Illuminate\Database\Eloquent\Model;

class EditMock extends EditRecord
{
    protected static string $resource = MockResource::class;

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
        // Skills va ularning Parts'larini yuklash
        if ($this->record->skills) {
            $data['skills'] = $this->record->skills->map(function ($skill) {
                $mockSkill = MockSkill::where('mock_id', $this->record->id)
                    ->where('skill_id', $skill->id)
                    ->first();

                return [
                    'id' => $skill->id,
                    'pivot' => [
                        'title' => $skill->pivot->title,
                        'text' => $skill->pivot->text,
                        'audio' => $skill->pivot->audio,
                        'photo' => $skill->pivot->photo,
                    ],
                    'parts' => $mockSkill->parts->map(function ($part) {
                        $mockSkillPartId = $part->pivot->id;

                        return [
                            'part_id' => $part->id,
                            'waiting_time' => $part->pivot->waiting_time,
                            'timer' => $part->pivot->timer,
                            'title' => $part->pivot->title,
                            'text' => $part->pivot->text,
                            'audio' => $part->pivot->audio,
                            'photo' => $part->pivot->photo,
                            'questions' => \App\Models\MockQuestion::where('mock_skill_part_id', $mockSkillPartId)
                                ->get()
                                ->map(function ($mockQuestion) {
                                    return [
                                        'question_id' => $mockQuestion->question_id,
                                        'limit_taymer' => $mockQuestion->limit_taymer,
                                    ];
                                })
                                ->toArray(),
                        ];
                    })->toArray(),
                ];
            })->toArray();
        }

        return $data;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        // Mock asosiy ma'lumotlarini yangilash
        $record->update([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
        ]);

        // Eski skills, parts va questions ni o'chirish
        foreach ($record->skills as $skill) {
            $mockSkill = MockSkill::where('mock_id', $record->id)
                ->where('skill_id', $skill->id)
                ->first();

            if ($mockSkill) {
                // MockQuestion'larni o'chirish
                foreach ($mockSkill->parts as $part) {
                    \App\Models\MockQuestion::where('mock_skill_part_id', $part->pivot->id)->delete();
                }
                // Part'larni o'chirish
                $mockSkill->parts()->detach();
            }
        }
        $record->skills()->detach();

        // Yangi skills va parts qo'shish
        if (isset($data['skills']) && count($data['skills']) > 0) {
            foreach ($data['skills'] as $skillData) {
                $record->skills()->attach($skillData['id'], [
                    'title' => $skillData['pivot']['title'] ?? null,
                    'text' => $skillData['pivot']['text'] ?? null,
                    'audio' => $skillData['pivot']['audio'] ?? null,
                    'photo' => $skillData['pivot']['photo'] ?? null,
                ]);

                $mockSkillId = $record->skills()
                    ->where('skill_id', $skillData['id'])
                    ->first()
                    ->pivot
                    ->id;

                if (isset($skillData['parts']) && count($skillData['parts']) > 0) {
                    $mockSkillModel = MockSkill::find($mockSkillId);

                    foreach ($skillData['parts'] as $partData) {
                        $mockSkillModel->parts()->attach($partData['part_id'], [
                            'waiting_time' => $partData['waiting_time'] ?? 0,
                            'timer' => $partData['timer'] ?? 0,
                            'title' => $partData['title'] ?? null,
                            'text' => $partData['text'] ?? null,
                            'audio' => $partData['audio'] ?? null,
                            'photo' => $partData['photo'] ?? null,
                        ]);

                        // MockSkillPart ID ni olish
                        $mockSkillPartId = $mockSkillModel->parts()
                            ->where('part_id', $partData['part_id'])
                            ->first()
                            ->pivot
                            ->id;

                        // Har bir Part uchun Question'larni bog'lash
                        if (isset($partData['questions']) && count($partData['questions']) > 0) {
                            foreach ($partData['questions'] as $questionData) {
                                \App\Models\MockQuestion::create([
                                    'question_id' => $questionData['question_id'],
                                    'mock_skill_part_id' => $mockSkillPartId,
                                    'limit_taymer' => $questionData['limit_taymer'] ?? 0,
                                ]);
                            }
                        }
                    }
                }
            }
        }

        return $record;
    }
}
