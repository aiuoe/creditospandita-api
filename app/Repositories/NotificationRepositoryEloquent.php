<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\NotificationRepository;
use App\Models\Notification;
use App\Validators\NotificationValidator;

/**
 * Class NotificationRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class NotificationRepositoryEloquent extends BaseRepository implements NotificationRepository
{
    /**
      * @var array
    */
    protected $fieldSearchable = [
        'title'             =>  'like',
        'body'              =>  'like',
        'users.email'       =>  'like',
        'users.first_name'  =>  'like',
        'users.last_name'   =>  'like',
    ];

    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Notification::class;
    }



    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

}
