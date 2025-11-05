<?php

namespace App\Models;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use App\Models\Question;
use App\Models\Skill;
use App\Models\Part;
use App\Models\Mock;
class MockQuestion extends Model
{
    use SoftDeletes;

    protected $table = 'mock_questions';
    protected $fillable = ['question_id', 'mock_skill_id', 'part_id', 'limit_taymer'];

    public function question()
    {
        return $this->belongsTo(Question::class, 'question_id');
    }

    public function part()
    {
        return $this->belongsTo(Part::class, 'part_id');
    }

    /**
     * MockSkill pivot jadvaliga bog'lanish
     * Bu orqali Mock va Skill larga ham kirishimiz mumkin
     */
    public function mockSkill()
    {
        return $this->belongsTo(MockSkill::class, 'mock_skill_id');
    }
}
