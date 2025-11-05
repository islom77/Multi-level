<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Part;
use App\Models\Skill;
use App\Models\QuestionType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\AdminUserSeeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        if(User::where('id', 1)->count() == 0){
            $this->call(AdminUserSeeder::class);
        }
        if(Part::all()->count() == 0){
            $this->call(PartSeeder::class);
        }
        if(Skill::all()->count() == 0){
            $this->call(SkillSeeder::class);
        }
        if(QuestionType::all()->count() == 0){
            $this->call(QuestionTypeSeeder::class);
        }
    }
}
