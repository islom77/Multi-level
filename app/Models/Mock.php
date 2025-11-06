<?php

namespace App\Models;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use App\Models\MockQuestion;
use App\Models\Part;


class Mock extends Model
{
    use SoftDeletes;
    protected $table = 'mocks';
    protected $fillable = ['name', 'description'];

    public function mockQuestions()
    {
        return $this->hasMany(MockQuestion::class, 'mock_id');
    }

    /**
     * Many-to-Many relationship with Skills
     * Pivot jadval: mock_skill (title, text, audio, photo bilan)
     */
    public function skills()
    {
        return $this->belongsToMany(Skill::class, 'mock_skill')
            ->withPivot(['title', 'text', 'audio', 'photo'])
            ->withTimestamps()
            ->using(MockSkill::class);
    }
}

