<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\CurrencyRepository;
use App\Models\Currency;
use App\Validators\CurrencyValidator;

/**
 * Class CurrencyRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class CurrencyRepositoryEloquent extends BaseRepository implements CurrencyRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Currency::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
