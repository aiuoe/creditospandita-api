<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactHistory extends Model
{
    protected $table = 'contact_history';
    public $timestamps = false;
    protected $fillable = [
        'idSolicitudFk',
        'idUserFk',
        'fechaContacto',
        'colaborador_id',
        'proposito',
        'metodoContacto',
        'resultado',
        'comentario',
        'fechaPtp',
        'montoPtp'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'created_at',
    ];

    public function colaborador()
    {
        return $this->belongsTo(User::class, 'colaborador_id');
    }
}
