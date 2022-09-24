<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\QuotationRepository;
use App\Models\Quotation;
use App\Validators\QuotationValidator;

/**
 * Class QuotationRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class QuotationRepositoryEloquent extends BaseRepository implements QuotationRepository
{

    /**
      * @var array
    */
    protected $fieldSearchable = [
        'folio'                         =>  'like',
        'currency.name'                 =>  'like',
        'status.name'                   =>  'like',
        'client.name'                   =>  'like',
        'client.rfc'                    =>  'like',
        'client.type'                   =>  'like',
        'client.email'                  =>  'like',
        'client.country.name'           =>  'like',
        'observation'                   =>  'like',
        'passengers.first_name'         =>  'like',
        'passengers.last_name'          =>  'like',
        'passengers.premier_number'     =>  'like',
        'passengers.nationalities.passport' =>  'like',
        'passengers.nationalities.visa'     =>  'like',
        'passengers.nationalities.ine'      =>  'like',
        'passengers.nationalities.curp'     =>  'like',
        'details.service.name'              =>  'like',
        'destinations.name'                 =>  'like',
        'destinations.description'          =>  'like',


    ];

    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Quotation::class;
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
