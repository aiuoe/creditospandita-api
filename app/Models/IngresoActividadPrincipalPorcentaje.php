<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IngresoActividadPrincipalPorcentaje extends Model
{
        //
        protected $fillable = [
            'id','nombre', 'puntaje'
        ];
        
        /**
         * The attributes that should be hidden for arrays.
         *
         * @var array
         */
        protected $hidden = [
             
        ];
}
