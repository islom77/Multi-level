<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletes;

class MockPart extends Pivot
{
    use SoftDeletes;

    protected $table = 'mock_part';

    // Pivot model uchun incrementing true qilish kerak (chunki id bor)
    public $incrementing = true;

    protected $fillable = [
        'mock_id',
        'part_id',
        'waiting_time',
        'timer',
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
     * Part relationship
     */
    public function part()
    {
        return $this->belongsTo(Part::class, 'part_id');
    }
}
