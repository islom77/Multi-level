<?php

namespace App\Models;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use App\Models\MockQuestion;
use App\Models\Part;
use App\Models\Time;


class Mock extends Model
{
    use SoftDeletes;
    protected $table = 'mocks';
    protected $fillable = ['name', 'description'];

    public function parts()
    {
        return $this->hasMany(Part::class, 'mock_id');
    }

    public function times()
    {
        return $this->hasMany(Time::class, 'mock_id');
    }

    public function mockQuestions()
    {
        return $this->hasMany(MockQuestion::class, 'mock_id');
    }
}

