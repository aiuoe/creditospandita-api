<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\EvaluacionRepository;
use App\Models\Evaluacion;
use App\Validators\UserValidator;

/**
 * Class UserRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class EvaluacionRepositoryEloquent extends BaseRepository implements EvaluacionRepository
{

    /**
      * @var array
    */
    protected $fieldSearchable = [
        'calculadora.numero_credito' =>  'like',
        'estatus',

    ];
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Evaluacion::class;
    }



    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

}
