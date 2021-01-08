<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Doctor extends Model
{
    use HasFactory, SoftDeletes;

    protected $softCascade = ['schedules'];

    protected $table = 'doctors';

    protected $fillable = [
        'category_id',
        'first_names',
        'last_names',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }
}
