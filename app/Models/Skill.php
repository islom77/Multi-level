<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\MockQuestion;
class Skill extends Model
{
    use SoftDeletes;
    protected $table = 'skills';
    protected $fillable = ['name'];

    public function mockQuestion()
    {
        return $this->hasMany(MockQuestion::class, 'skill_id');
    }

    /**
     * Many-to-Many relationship with Mocks
     * Pivot jadval: mock_skill (title, text, audio, photo bilan)
     */
    public function mocks()
    {
        return $this->belongsToMany(Mock::class, 'mock_skill')
            ->withPivot(['title', 'text', 'audio', 'photo'])
            ->withTimestamps();
    }
}

