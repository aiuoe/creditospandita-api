<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CodigosValidaciones extends Model
{
    protected $fillable = [
        'id', 'codigo','idUserFk','idSolicitudFk','valido','token_firma','created_at','updated_at'
    ];
    //
}
