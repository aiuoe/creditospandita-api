<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\QuotationDetailRepository;
use App\Models\QuotationDetail;
use App\Validators\QuotationDetailValidator;

/**
 * Class QuotationDetailRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class QuotationDetailRepositoryEloquent extends BaseRepository implements QuotationDetailRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return QuotationDetail::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
