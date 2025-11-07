<?php

namespace App\Models;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use App\Models\Question;
use App\Models\MockSkillPart;

class MockQuestion extends Model
{
    use SoftDeletes;

    protected $table = 'mock_questions';
    protected $fillable = ['question_id', 'mock_skill_part_id', 'limit_taymer', 'order'];

    public function question()
    {
        return $this->belongsTo(Question::class, 'question_id');
    }

    /**
     * MockSkillPart pivot jadvaliga bog'lanish
     * Bu orqali Mock, Skill va Part larga ham kirishimiz mumkin
     */
    public function mockSkillPart()
    {
        return $this->belongsTo(MockSkillPart::class, 'mock_skill_part_id');
    }
}
