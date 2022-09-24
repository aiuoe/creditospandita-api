<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\CollaboratorRepository;
use App\Models\Collaborator;
use App\Validators\CollaboratorValidator;

/**
 * Class CollaboratorRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class CollaboratorRepositoryEloquent extends BaseRepository implements CollaboratorRepository
{
    /**
      * @var array
    */
    protected $fieldSearchable = [
        'first_name'                =>  'like',
        'last_name'                 =>  'like',
        'work_place'                =>  'like',
        'birthdate',
        'date_admission',
        'curp',
        'ine',
        'nss',
        'rfc',
        'visa',
        'passport',
        'contract_conditions'       =>  'like',
        'blood_type.name'           =>  'like',
    ];
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Collaborator::class;
    }



    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

}
