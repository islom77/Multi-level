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
}

