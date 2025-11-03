<?php

namespace App\Models;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use App\Models\QuestionData;
use App\Models\Question;
class KeyWord extends Model
{
    use SoftDeletes;
    protected $table = 'key_words';
    protected $fillable = ['question_data_id', 'question_id', 'word'];

    public function questionData()
    {
        return $this->belongsTo(QuestionData::class, 'question_data_id');
    }

    public function question()
    {
        return $this->belongsTo(Question::class, 'question_id');
    }
}
