<?php

namespace App\Criteria;

use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class RoleCriteria.
 *
 * @package namespace App\Criteria;
 */
class RoleCriteria implements CriteriaInterface
{
    public function __construct()
    {

    }
    /**
     * Apply criteria in query repository
     *
     * @param string              $model
     * @param RepositoryInterface $repository
     *
     * @return mixed
     */
    public function apply($model, RepositoryInterface $repository)
    {
        $search = request()->get(config('repository.criteria.params.search', 'search'), null);
        $until = request()->get(config('repository.criteria.params.until', 'until'), null);
        $since = request()->get(config('repository.criteria.params.since', 'since'), null);

        if(!empty($since))
        {
            $model = $model->whereDate('created_at','>=',$since);
        }

        if(!empty($until))
        {
            $model = $model->whereDate('created_at','<=',$until);
        }

        return $model;
    }
}
