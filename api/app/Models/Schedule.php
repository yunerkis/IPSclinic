<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Schedule extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'schedules';

    protected $fillable = [
        'doctor_id',
        'dayWeeks',
        'dates',
        'time_start',
        'time_end',
        'time',
        'availability'
    ];

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }
}
