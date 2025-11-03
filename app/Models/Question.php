<?php

namespace App\Models;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use App\Models\QuestionType;
use App\Models\Option;
use App\Models\Answer;
use App\Models\QuestionData;
use App\Models\MockQuestion;

class Question extends Model
{
    use SoftDeletes;
    protected $table = 'questions';
    protected $fillable = ['question_type_id', 'name', 'ota_id'];

    public function questionType()
    {
        return $this->belongsTo(QuestionType::class, 'question_type_id');
    }

    public function options()
    {
        return $this->hasMany(Option::class, 'question_id');
    }

    public function answers()
    {
        return $this->hasMany(Answer::class, 'question_id');
    }

    public function questionData()
    {
        return $this->hasOne(QuestionData::class, 'question_id');
    }

    public function mockQuestion()
    {
        return $this->hasMany(MockQuestion::class, 'question_id');
    }
}
