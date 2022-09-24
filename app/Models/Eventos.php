<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Eventos extends Model
{

    protected $fillable = [
        'id', 'evento','ip','coordenadas','email_usuario'
    ];
}
