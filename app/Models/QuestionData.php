<?php

namespace App\Models;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use App\Models\Question;
use App\Models\KeyWord;

class QuestionData extends Model
{
    use SoftDeletes;
    protected $table = 'question_data';
    protected $fillable = ['text', 'audio', 'question_id'];

    public function question()
    {
        return $this->belongsTo(Question::class, 'question_id');
    }

    public function keyWords()
    {
        return $this->hasMany(KeyWord::class, 'question_data_id');
    }
}

