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
    protected $table = 'mock_questions';
    protected $fillable = ['question_id', 'skill_id', 'part_id', 'mock_id', 'limit_taymer'];

    public function question()
    {
        return $this->belongsTo(Question::class, 'question_id');
    }

    public function skill()
    {
        return $this->belongsTo(Skill::class, 'skill_id');
    }

    public function part()
    {
        return $this->belongsTo(Part::class, 'part_id');
    }

    public function mock()
    {
        return $this->belongsTo(Mock::class, 'mock_id');
    }
}
