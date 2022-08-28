<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Result extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'results';

    protected $fillable = [
        'client_id',
        'user_id',
        'filename',
        'url'
    ];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d',
    ];
}
