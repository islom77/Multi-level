<?php

namespace App\Models;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use App\Models\MockQuestion;


class Part extends Model
{
    use SoftDeletes;
    protected $table = 'part';
    protected $fillable = ['mock_id', 'name'];


    public function mockQuestions()
    {
        return $this->hasMany(MockQuestion::class, 'part_id');
    }
}

