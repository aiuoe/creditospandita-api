<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\BloodTypeRepository;
use App\Models\BloodType;
use App\Validators\BloodTypeValidator;

/**
 * Class BloodTypeRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class BloodTypeRepositoryEloquent extends BaseRepository implements BloodTypeRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return BloodType::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
