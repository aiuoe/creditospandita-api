<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class Passenger.
 *
 * @package namespace App\Models;
 */
class Passenger extends Model implements Transformable
{
    use TransformableTrait, SoftDeletes, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'premier_number',
        'birthdate',
        'email',
        'mobile',
        'phone',
        'observation',
        'country_id',
        'city',
        'street',
        'colony',
        'zip_code',
        'ine',
        'curp'
    ];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'birthdate'              =>  'date',
        'created_at'            =>  'datetime',
        'updated_at'            =>  'datetime'
    ];

    protected $appends = [
        'LastDestination'
    ];
    /**
     * Relations
     */
    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function emergency_contacts()
    {
        return $this->hasMany(PassengerEmergencyContact::class);
    }

    public function nationalities()
    {
        return $this->hasMany(PassengerNationality::class);
    }

    public function quotations()
    {
        return $this->belongsToMany(Quotation::class);
    }

    public function files()
    {
        return $this->belongsToMany(File::class,'relation_file','rel_id','file_id')
        ->where('table_name',$this->getTable());
    }

    public function getLastDestinationAttribute()
    {
        $quotation = $this->quotations()->whereHas('status',function($q){
            $q->whereIn('name',['Aprobada','Aprobada y pagada']);
        })->orderBy('approved_date','desc')->first();

        if(!empty($quotation))
        {
            return $quotation->destinations->last()->name;
        }else{
            return '';
        }

    }

}
