<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\PropoalRepository;
use App\Models\Propoal;
use App\Validators\PropoalValidator;

/**
 * Class PropoalRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class PropoalRepositoryEloquent extends BaseRepository implements PropoalRepository
{


    /**
      * @var array
    */
    protected $fieldSearchable = [
        'folio'                             =>  'like',
        'client.name'                       =>  'like',
        'client.rfc'                        =>  'like',
        'client.email'                      =>  'like',
        'client.type'                       =>  'like',
        'client.country.name'               =>  'like',
        'client.city'                       =>  'like',
        'client.street'                     =>  'like',
        'client.colony'                     =>  'like',
        'client.zip_code'                   =>  'like',
        'observation'                       =>  'like',
        'expiration_date'                   =>  '=',
        'status.name'                       =>  'like'
    ];

    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Propoal::class;
    }

    /**
     * Instance a new entity in repository
     *
     *
     * @param array $attributes
     *
     * @return mixed
     */
    public function new(array $attributes)
    {
        $model = $this->model->newInstance();
        $model->fill( $attributes );
        return $model;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

}
