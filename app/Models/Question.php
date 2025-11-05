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
    protected $fillable = ['question_type_id', 'name', 'parent_id'];

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

    // Ota-savol uchun relationship (ierarxik savollar)
    public function parent()
    {
        return $this->belongsTo(Question::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Question::class, 'parent_id');
    }

    // KeyWords - to'g'ridan-to'g'ri relationship (key_words jadvalida question_id bor)
    public function keyWords()
    {
        return $this->hasMany(\App\Models\KeyWord::class, 'question_id');
    }
}
