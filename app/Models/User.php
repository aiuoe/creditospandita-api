<?php

namespace App\Models;
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;
use Spatie\Permission\Traits\HasRoles;


class User extends Authenticatable implements Transformable
{
    use TransformableTrait, HasApiTokens, Notifiable, SoftDeletes, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */


    protected $fillable = [
        'email', 'first_name', 'second_name', 'last_name', 'second_last_name','n_document','phone_number','password','token_password','borrower_id_Fk','estatus',
        'token_firma','ip','coordenadas','codigoReferidor',"tipoDocumento","notificado","tokenFb","tokenAnalizer"
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
         'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' =>  'datetime',
        'created_at'        =>  'datetime'
    ];

    /**
     *
     * Setters attributers
     */
    public function setPasswordAttribute($password)
    {
        $this->attributes['password'] = bcrypt($password);
    }


    protected $appends = [
        'LastSolicitud',
        'FirstSolicitud',
        'ModulosActivos'
    ];

    // public function quotations()
    // {
    //     return $this->belongsToMany(Calculadora::class);
    // }
    public function lista_modulos(){
        return $this->hasMany(Modulos::class, 'idUserFk','id');
    }

    public function calculadora()
    {
        return $this->hasMany(Calculadora::class, 'idUserFk','id');
    }




    public function getLastSolicitudAttribute()
    {
        $quotation = $this->calculadora()->orderBy('created_at','desc')->first();

        if(!empty($quotation))
        {
            return $quotation;
        }else{
            return '';
        }

    }
    public function getFirstSolicitudAttribute()
    {
        $quotation = $this->calculadora()->orderBy('created_at','asc')->first();

        if(!empty($quotation))
        {
            return $quotation;
        }else{
            return '';
        }

    }

    public function getModulosActivosAttribute()
    {
        $modulos = $this->lista_modulos()->first();

        if(!empty($modulos))
        {
            return $modulos;
        }else{
            return '';
        }

    }

    public function basica()
    {
        return $this->hasOne(Basica::class, 'idUserFk');
    }

    public function financiera()
    {
        return $this->hasOne(Financiera::class, 'idUserFk');
    }


}
