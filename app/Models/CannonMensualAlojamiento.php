<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CannonMensualAlojamiento extends Model
{
        //
        protected $fillable = [
            'id','estrato', 'alojamiento', 'monto'
        ];
        
        /**
         * The attributes that should be hidden for arrays.
         *
         * @var array
         */
        protected $hidden = [
             
        ];
}
