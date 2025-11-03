<?php

namespace App\Models;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use App\Models\Question;

class Answer extends Model
{
    use SoftDeletes;
    protected $table = 'answers';
    protected $fillable = ['student_id', 'question_id', 'answer'];

    public function question()
    {
        return $this->belongsTo(Question::class, 'question_id');
    }
}

