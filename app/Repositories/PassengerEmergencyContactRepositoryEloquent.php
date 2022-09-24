<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\PassengerEmergencyContactRepository;
use App\Models\PassengerEmergencyContact;
use App\Validators\PassengerEmergencyContactValidator;

/**
 * Class PassengerEmergencyContactRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class PassengerEmergencyContactRepositoryEloquent extends BaseRepository implements PassengerEmergencyContactRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return PassengerEmergencyContact::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
