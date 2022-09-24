<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Referencias extends Model
{
     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
  
    protected $fillable = [
        'ReferenciaPersonalNombres', 'ReferenciaPersonalApellidos', 'ReferenciaPersonalCiudadFk', 'ReferenciaPersonalTelefono', 'ReferenciaFamiliarNombres',
        'ReferenciaFamiliarApellidos','ReferenciaFamiliarCiudadFk','ReferenciaFamiliarTelefono','QuienRecomendo','relacionp','relacionf','emailp','emailf','iduserFk'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'id', 
    ];

}
