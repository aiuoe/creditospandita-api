<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\PassengerRepository;
use App\Models\Passenger;
use App\Validators\PassengerValidator;

/**
 * Class PassengerRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class PassengerRepositoryEloquent extends BaseRepository implements PassengerRepository
{
    /**
      * @var array
    */
    protected $fieldSearchable = [
        'first_name'                    =>  'like',
        'last_name'                     =>  'like',
        'premier_number'                =>  'like',
        'email'                         =>  'like',
        'country.name'                  =>  'like',
        'city'                          =>  'like',
        'street'                        =>  'like',
        'colony'                        =>  'like',
        'phone'                         =>  'like',
        'mobile'                        =>  'like',
        'zip_code'                      =>  'like',
        'observation'                   =>  'like',
        'nationalities.country.name'    =>  'like',
        'nationalities.passport'        =>  'like',
        'nationalities.visa'            =>  'like',
        'nationalities.ine'             =>  'like',
        'nationalities.curp'            =>  'like',
        'quotations.client.name'        =>  'like',
        'quotations.client.rfc'         =>  'like',
        'quotations.client.type'        =>  'like',
        'quotations.client.name'        =>  'like',
        'quotations.destinations.name'  =>  'like',
    ];
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Passenger::class;
    }



    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

}
