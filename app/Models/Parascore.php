<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Parascore extends Model
{
    protected $fillable = [
        'desde', 'hasta', 'caso'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'created_at', 
    ];
}
