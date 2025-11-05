<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use App\Models\Mock;
use App\Models\Part;

class Time extends Model
{
    use SoftDeletes;
    protected $table = 'time';
    protected $fillable = ['mock_id', 'part_id', 'timer', 'description', 'audio_file', 'preperation_time'];

    public function mock()
    {
        return $this->belongsTo(Mock::class, 'mock_id');
    }

    public function part()
    {
        return $this->belongsTo(Part::class, 'part_id');
    }
}
