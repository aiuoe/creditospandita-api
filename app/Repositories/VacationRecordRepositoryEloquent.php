<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\VacationRecordRepository;
use App\Models\VacationRecord;
use App\Validators\VacationRecordValidator;

/**
 * Class VacationRecordRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class VacationRecordRepositoryEloquent extends BaseRepository implements VacationRecordRepository
{

    /**
      * @var array
    */
    protected $fieldSearchable = [
        'days',
        'observation'                            =>  'like',
        'collaborator.first_name'                =>  'like',
        'collaborator.last_name'                 =>  'like',
        'collaborator.work_place'                =>  'like',
        'collaborator.curp',
        'collaborator.ine',
        'collaborator.nss',
        'collaborator.rfc',
        'collaborator.visa',
        'collaborator.passport',
        'collaborator.contract_conditions'       =>  'like',
        'collaborator.blood_type.name'           =>  'like',
    ];
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return VacationRecord::class;
    }



    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

}
