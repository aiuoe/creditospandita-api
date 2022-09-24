<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notificaciones extends Model
{
    protected $fillable = [
        'id', 'titulo', 'mensaje', 
        'idEvaluacionFk',
        'idSolicitudFk',
        'idUserFk',
        'codigo',
        'created_at'
    ];
}
