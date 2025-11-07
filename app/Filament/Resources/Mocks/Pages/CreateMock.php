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
                $mock->skills()->attach($skillData['id'], [
                    'title' => $skillData['pivot']['title'] ?? null,
                    'text' => $skillData['pivot']['text'] ?? null,
                    'audio' => $skillData['pivot']['audio'] ?? null,
                    'photo' => $skillData['pivot']['photo'] ?? null,
                ]);

                // MockSkill ni olish (yangi yaratilgan pivot yozuvni)
                $mockSkill = MockSkill::where('mock_id', $mock->id)
                    ->where('skill_id', $skillData['id'])
                    ->first();

                // Har bir Skill uchun Part'larni bog'lash
                if ($mockSkill && isset($skillData['parts']) && count($skillData['parts']) > 0) {
                    foreach ($skillData['parts'] as $partData) {
                        $mockSkill->parts()->attach($partData['part_id'], [
                            'waiting_time' => $partData['waiting_time'] ?? 0,
                            'timer' => $partData['timer'] ?? 0,
                            'title' => $partData['title'] ?? null,
                            'text' => $partData['text'] ?? null,
                            'audio' => $partData['audio'] ?? null,
                            'photo' => $partData['photo'] ?? null,
                        ]);

                        // MockSkillPart ID ni to'g'ridan-to'g'ri MockSkillPart jadvalidan olish
                        $mockSkillPart = \App\Models\MockSkillPart::where('mock_skill_id', $mockSkill->id)
                            ->where('part_id', $partData['part_id'])
                            ->first();

                        // Har bir Part uchun Question'larni bog'lash
                        if ($mockSkillPart && isset($partData['questions']) && count($partData['questions']) > 0) {
                            foreach ($partData['questions'] as $questionData) {
                                \App\Models\MockQuestion::create([
                                    'question_id' => $questionData['question_id'],
                                    'mock_skill_part_id' => $mockSkillPart->id,
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
