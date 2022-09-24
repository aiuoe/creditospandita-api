<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\UserRepository;
use App\Models\User;
use App\Validators\UserValidator;

/**
 * Class UserRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class UserRepositoryEloquent extends BaseRepository implements UserRepository
{

    /**
      * @var array
    */
    protected $fieldSearchable = [
        'calculadora.numero_credito' =>  'like',
        'calculadora.estatus'=>  'like',
        'last_name'         =>  'like',
        'first_name'         =>  'like',
        'n_document'=> 'like',
        'email'             =>  'like',
        'phone_number'             =>  'like'
    ];
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return User::class;
    }



    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

}
