<?php

namespace App\Filament\Resources\Mocks\Pages;

use App\Filament\Resources\Mocks\MockResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use App\Models\MockSkill;

class CreateMock extends CreateRecord
{
    protected static string $resource = MockResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        // Mock yaratish
        $mock = static::getModel()::create([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
        ]);

        // Skills va Parts yaratish
        if (isset($data['skills']) && count($data['skills']) > 0) {
            foreach ($data['skills'] as $skillData) {
                // MockSkill yaratish (pivot entry)
                $mockSkill = $mock->skills()->attach($skillData['id'], [
                    'title' => $skillData['pivot']['title'] ?? null,
                    'text' => $skillData['pivot']['text'] ?? null,
                    'audio' => $skillData['pivot']['audio'] ?? null,
                    'photo' => $skillData['pivot']['photo'] ?? null,
                ]);

                // MockSkill ID ni olish
                $mockSkillId = $mock->skills()
                    ->where('skill_id', $skillData['id'])
                    ->first()
                    ->pivot
                    ->id;

                // Har bir Skill uchun Part'larni bog'lash
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

        return $mock;
    }
}
