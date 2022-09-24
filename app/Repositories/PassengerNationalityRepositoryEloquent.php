<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\PassengerNationalityRepository;
use App\Models\PassengerNationality;
use App\Validators\PassengerNationalityValidator;

/**
 * Class PassengerNationalityRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class PassengerNationalityRepositoryEloquent extends BaseRepository implements PassengerNationalityRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return PassengerNationality::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
