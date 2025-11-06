<?php

namespace App\Models;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use App\Models\MockQuestion;

class Part extends Model
{
    protected $fillable = ['name'];

    /**
     * MockSkill bilan many-to-many relationship
     * Pivot: mock_skill_part
     */
    public function mockSkills()
    {
        return $this->belongsToMany(MockSkill::class, 'mock_skill_part', 'part_id', 'mock_skill_id')
            ->withPivot(['waiting_time', 'timer', 'title', 'text', 'audio', 'photo'])
            ->withTimestamps()
            ->using(MockSkillPart::class);
    }
}

