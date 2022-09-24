<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\QuotationDestinationRepository;
use App\Models\QuotationDestination;
use App\Validators\QuotationDestinationValidator;

/**
 * Class QuotationDestinationRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class QuotationDestinationRepositoryEloquent extends BaseRepository implements QuotationDestinationRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return QuotationDestination::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
