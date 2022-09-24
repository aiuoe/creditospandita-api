<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\OfficeRepository;
use App\Models\Office;
use App\Validators\OfficeValidator;

/**
 * Class OfficeRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class OfficeRepositoryEloquent extends BaseRepository implements OfficeRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Office::class;
    }



    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }


    public function filter($params)
    {
        $order = (!empty($request->order_by)) ? $request->order_by : 'name';
        $orderType = (!empty($request->order_type)) ? $request->order_type : 'asc';


        $query = $this->orderBy($order, $orderType);

        if( !empty($request->text) )
        {
            $words = explode(' ', $request->text);
            $query->where(function($q) use($words){
                foreach( $words as $word )
                {

                    $q->orWhere('name','like', '%'.$word.'%');
                    $q->orWhere('contact','like', '%'.$word.'%');
                    $q->orWhere('city','like', '%'.$word.'%');
                    $q->orWhere('street','like', '%'.$word.'%');
                    $q->orWhere('colony','like', '%'.$word.'%');
                    $q->orWhere('zip_code','like', '%'.$word.'%');
                    $q->orWhere('email','like', '%'.$word.'%');
                    $q->orWhere('phone_1','like', '%'.$word.'%');
                    $q->orWhere('phone_2','like', '%'.$word.'%');
                    $q->orWhere('observation','like', '%'.$word.'%');
                }
            });
        }

        return $query;
    }

}
