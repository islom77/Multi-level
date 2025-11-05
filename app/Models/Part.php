<?php

namespace App\Models;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use App\Models\MockQuestion;

class Part extends Model
{
    protected $fillable = ['name'];

    public function mockQuestions()
    {
        return $this->hasMany(MockQuestion::class, 'part_id');
    }

    /**
     * Many-to-Many relationship with Mocks
     * Pivot jadval: mock_part (waiting_time, timer, title, text, audio, photo)
     */
    public function mocks()
    {
        return $this->belongsToMany(Mock::class, 'mock_part')
            ->withPivot(['waiting_time', 'timer', 'title', 'text', 'audio', 'photo'])
            ->withTimestamps();
    }
}

