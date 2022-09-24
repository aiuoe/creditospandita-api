<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\ClientNationalityRepository;
use App\Models\ClientNationality;
use App\Validators\ClientNationalityValidator;

/**
 * Class ClientNationalityRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class ClientNationalityRepositoryEloquent extends BaseRepository implements ClientNationalityRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return ClientNationality::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
