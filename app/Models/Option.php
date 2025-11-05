<?php

namespace App\Models;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use App\Models\Question;
class Option extends Model
{
    use SoftDeletes;
    protected $table = 'options';
    protected $fillable = ['question_id', 'title', 'text', 'order'];

    /**
     * Option harfini olish (A, B, C, D, E, F...)
     *
     * @return string
     */
    public function getLetterAttribute()
    {
        if ($this->order === null) {
            return '';
        }
        return chr(65 + $this->order); // 65 = 'A' ASCII code
    }

    public function question()
    {
        return $this->belongsTo(Question::class, 'question_id');
    }

    // Bu option to'g'ri javob bo'lgan questionlar
    public function trueForQuestions()
    {
        return $this->hasMany(Question::class, 'true_option_id');
    }
}
