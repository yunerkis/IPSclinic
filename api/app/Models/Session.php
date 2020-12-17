<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Session extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'sessions';

    protected $fillable = [
        'client_id',
        'date',
        'time_start',
        'time_end'
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
