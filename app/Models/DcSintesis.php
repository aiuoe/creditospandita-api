<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DcSintesis extends Model
{
    protected $table= "dc_sintesis";

    protected $fillable = [
        'id', 'idEvaluacion','estadoDocumento','nroCedula','fechaExpedicion', 'genero',
        'rangoEdad'
    ];
}
