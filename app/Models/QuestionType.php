<?php

namespace App\Models;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use App\Models\Question;
class QuestionType extends Model
{
    use SoftDeletes;
    protected $table = 'questions_type';
    protected $fillable = ['name'];

    public function questions()
    {
        return $this->hasMany(Question::class, 'question_type_id');
    }
}

