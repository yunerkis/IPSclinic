<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;

class Client extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'clients';

    protected $fillable = [
        'dni',
        'first_names',
        'last_names_1',
        'last_names_2',
        'status'
    ];

    public function sessions()
    {
        return $this->hasMany(Session::class);
    }

    public function scopeFilter($query, Request $request)
    {
        $search = trim($request['search']) != "" ? trim($request['search']) : NULL;
       
        if ($search) {
            
            $query->where('dni', $search)->orWhere('first_names', 'LIKE', '%' . $search . '%')
                                ->orWhere('last_names_1', 'LIKE', '%' . $search . '%')
                                ->orWhere('last_names_2', 'LIKE', '%' . $search . '%')
                                ->orWhere('status', $search);                      
        }
    }
}
