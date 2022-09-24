<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\TaskRepository;
use App\Models\Task;
use App\Validators\TaskValidator;

/**
 * Class TaskRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class TaskRepositoryEloquent extends BaseRepository implements TaskRepository
{

    /**
      * @var array
    */
    protected $fieldSearchable = [
        'client.first_name'                 =>  'like',
        'client.last_name'                  =>  'like',
        'client.nationalities.passport'     =>  'like',
        'client.nationalities.visa'         =>  'like',
        'name'                              =>  'like',
        'description'                       =>  'like',
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
        return Task::class;
    }



    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

}
