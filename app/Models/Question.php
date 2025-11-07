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
    protected $fillable = ['question_type_id', 'name', 'text', 'parent_id', 'true_option_id'];

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
        return $this->hasMany(QuestionData::class, 'question_id');
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

    // To'g'ri javob option'i (matching savollar uchun)
    public function trueOption()
    {
        return $this->belongsTo(Option::class, 'true_option_id');
    }

    /**
     * Foydalanuvchi javobini tekshirish (Matching savollar uchun)
     *
     * @param int $selectedOptionId - Foydalanuvchi tanlagan option ID
     * @return bool - To'g'ri/Noto'g'ri
     */
    public function checkMatchingAnswer($selectedOptionId)
    {
        // Agar bu child question bo'lsa va true_option_id mavjud bo'lsa
        if ($this->parent_id !== null && $this->true_option_id !== null) {
            return $this->true_option_id == $selectedOptionId;
        }

        return false;
    }

    /**
     * Matching savol uchun barcha ma'lumotlarni olish
     *
     * @return array
     */
    public function getMatchingData()
    {
        // Faqat parent question uchun
        if ($this->parent_id !== null) {
            return null;
        }

        return [
            'question' => [
                'id' => $this->id,
                'name' => $this->name,
                'text' => $this->text,
            ],
            'left_items' => $this->children->map(function ($child) {
                return [
                    'id' => $child->id,
                    'name' => $child->name,
                    'text' => $child->text,
                ];
            }),
            'right_items' => $this->options->map(function ($option) {
                return [
                    'id' => $option->id,
                    'title' => $option->title,
                    'text' => $option->text,
                ];
            })->shuffle(), // Aralashtirish
        ];
    }
}
