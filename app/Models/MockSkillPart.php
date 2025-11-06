<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletes;

class MockSkillPart extends Pivot
{
    use SoftDeletes;

    protected $table = 'mock_skill_part';

    public $incrementing = true;

    protected $fillable = [
        'mock_skill_id',
        'part_id',
        'waiting_time',
        'timer',
        'title',
        'text',
        'audio',
        'photo',
    ];

    /**
     * MockSkill bilan bog'lanish
     */
    public function mockSkill()
    {
        return $this->belongsTo(MockSkill::class, 'mock_skill_id');
    }

    /**
     * Part bilan bog'lanish
     */
    public function part()
    {
        return $this->belongsTo(Part::class, 'part_id');
    }

    /**
     * MockQuestion'lar
     */
    public function mockQuestions()
    {
        return $this->hasMany(MockQuestion::class, 'mock_skill_part_id');
    }
}
