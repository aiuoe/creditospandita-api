<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DcEndeudamiento extends Model
{
    protected $fillable = [
        'id', 'idEvaluacion','mes','mora'
    ];
}
