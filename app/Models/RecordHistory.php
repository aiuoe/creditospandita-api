<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecordHistory extends Model
{
    public $timestamps = true;

    protected $fillable = [
        'uuid',
        'idSolicitudFk',
        'idUserFk',
        'fecha_registro',
    ];

    protected $hidden = [
        'created_at',
    ];
}
