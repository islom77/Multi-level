<?php

namespace App\Models;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use App\Models\Question;
class Option extends Model
{
    use SoftDeletes;
    protected $table = 'options';
    protected $fillable = ['question_id', 'title', 'correct'];

    public function question()
    {
        return $this->belongsTo(Question::class, 'question_id');
    }
}
