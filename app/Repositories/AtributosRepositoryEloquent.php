<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\AtributosRepository;
use App\Models\Atributos;
use App\Validators\UserValidator;

/**
 * Class AtributosRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class AtributosRepositoryEloquent extends BaseRepository implements AtributosRepository
{

    /**
      * @var array
    */
    protected $fieldSearchable = [
        'variable' =>  'like',
        'categoria' =>  'like'
    ];
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Atributos::class;
    }



    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

}
