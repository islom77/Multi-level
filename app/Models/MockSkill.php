<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletes;

class MockSkill extends Pivot
{
    use SoftDeletes;

    protected $table = 'mock_skill';

    // Pivot model uchun incrementing true qilish kerak (chunki id bor)
    public $incrementing = true;

    protected $fillable = [
        'mock_id',
        'skill_id',
        'title',
        'text',
        'audio',
        'photo',
    ];

    /**
     * Mock relationship
     */
    public function mock()
    {
        return $this->belongsTo(Mock::class, 'mock_id');
    }

    /**
     * Skill relationship
     */
    public function skill()
    {
        return $this->belongsTo(Skill::class, 'skill_id');
    }

    /**
     * Bu MockSkill ga tegishli barcha MockQuestions
     */
    public function mockQuestions()
    {
        return $this->hasMany(MockQuestion::class, 'mock_skill_id');
    }
}
