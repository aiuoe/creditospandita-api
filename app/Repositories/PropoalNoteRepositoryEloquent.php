<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\PropoalNoteRepository;
use App\Models\PropoalNote;
use App\Validators\PropoalNoteValidator;

/**
 * Class PropoalNoteRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class PropoalNoteRepositoryEloquent extends BaseRepository implements PropoalNoteRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return PropoalNote::class;
    }



    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

}
