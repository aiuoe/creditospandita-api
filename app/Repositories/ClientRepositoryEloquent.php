<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\ClientRepository;
use App\Models\Client;
use App\Validators\ClientValidator;

/**
 * Class ClientRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class ClientRepositoryEloquent extends BaseRepository implements ClientRepository
{
    /**
      * @var array
    */
    protected $fieldSearchable = [
        'type'                      =>  'like',
        'name'                      =>  'like',
        'rfc'                       =>  'like',
        'contact_person'            =>  'like',
        'mobile'                    =>  'like',
        'phone'                     =>  'like',
        'email'                     =>  'like',
        'observation'               =>  'like',
        'country.name'              =>  'like',
        'city'                      =>  'like',
        'street'                    =>  'like',
        'colony'                    =>  'like',
        'zip_code'                  =>  '=',
        'payment_method.name'       =>  'like',
        'parent.name'               =>  'like',
        'parent.rfc'                =>  'like',
        'quotations.destinations.name'  =>  'like'
    ];

    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Client::class;
    }


    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

}
