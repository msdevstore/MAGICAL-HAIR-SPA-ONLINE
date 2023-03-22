<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;

class Holiday extends Model
{
    protected $fillable = [

        'title',
        'start_date',
        'end_date',
        'created_by',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
