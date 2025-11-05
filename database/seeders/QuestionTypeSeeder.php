<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\QuestionType;

class QuestionTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $questionTypes = [
            ['name' => 'Multiple Choice'],
            ['name' => 'True/False'],
            ['name' => 'Fill in the Blank'],
            ['name' => 'Short Answer'],
            ['name' => 'Essay'],
            ['name' => 'Matching'],
            ['name' => 'Listening Comprehension'],
            ['name' => 'Speaking Task'],
            ['name' => 'Instruction'], // Ko'rsatmalar uchun
        ];

        foreach ($questionTypes as $type) {
            QuestionType::firstOrCreate($type);
        }
    }
}
