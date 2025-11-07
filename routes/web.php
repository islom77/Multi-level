<?php

use Illuminate\Support\Facades\Route;
use App\Models\Mock;
use Barryvdh\DomPDF\Facade\Pdf;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/mocks/{mock}/pdf', function (Mock $mock) {
    // Mock Skills bilan birga MockSkill va MockSkillPart ma'lumotlarini olish
    $mockData = [];
    foreach ($mock->skills as $skill) {
        $mockSkill = \App\Models\MockSkill::where('mock_id', $mock->id)
            ->where('skill_id', $skill->id)
            ->first();

        $parts = [];
        if ($mockSkill) {
            foreach ($mockSkill->parts as $part) {
                // MockSkillPart ni to'g'ridan-to'g'ri topish
                $mockSkillPart = \App\Models\MockSkillPart::where('mock_skill_id', $mockSkill->id)
                    ->where('part_id', $part->id)
                    ->first();

                $questions = [];
                if ($mockSkillPart) {
                    $questions = \App\Models\MockQuestion::where('mock_skill_part_id', $mockSkillPart->id)
                        ->with('question.questionType', 'question.options', 'question.children.keyWords', 'question.questionData')
                        ->get();
                }

                $parts[] = [
                    'part' => $part,
                    'pivot' => $mockSkillPart,
                    'questions' => $questions,
                ];
            }
        }

        $mockData[] = [
            'skill' => $skill,
            'pivot' => $skill->pivot,
            'parts' => $parts,
        ];
    }

    $pdf = Pdf::loadView('pdf.mock', [
        'mock' => $mock,
        'mockData' => $mockData,
    ]);

    return $pdf->stream('mock-' . $mock->id . '-' . str_replace(' ', '-', $mock->name) . '.pdf');
})->name('mocks.pdf');
