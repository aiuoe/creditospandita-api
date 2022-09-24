<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\TaskTypeRepository;
use App\Models\TaskType;
use App\Validators\TaskTypeValidator;

/**
 * Class TaskTypeRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class TaskTypeRepositoryEloquent extends BaseRepository implements TaskTypeRepository
{
    /**
      * @var array
    */
    protected $fieldSearchable = [
        'name'      =>  'like'
    ];
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return TaskType::class;
    }



    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

}
