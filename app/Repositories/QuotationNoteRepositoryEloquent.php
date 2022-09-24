<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\QuotationNoteRepository;
use App\Models\QuotationNote;
use App\Validators\QuotationNoteValidator;

/**
 * Class QuotationNoteRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class QuotationNoteRepositoryEloquent extends BaseRepository implements QuotationNoteRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return QuotationNote::class;
    }



    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

}
