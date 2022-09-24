<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\PaymentRepository;
use App\Models\Payment;
use App\Validators\PaymentValidator;

/**
 * Class PaymentRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class PaymentRepositoryEloquent extends BaseRepository implements PaymentRepository
{
    /**
      * @var array
    */
    protected $fieldSearchable = [
        'client.country.name'           =>  'like',
        'client.name'                   =>  'like',
        'client.rfc'                    =>  'like',
        'client.email'                  =>  'like',
        'client.zip_code'               =>  'like',
        'currency.name'                 =>  'like',
        'payment_method.name'           =>  'like',
        'date_payment'                  =>  '=',
        'import'                        =>  '=',
        'concept'                       =>  'like',
        'observation'                   =>  'like',
        'folio_number'                  =>  'like'
    ];
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Payment::class;
    }



    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

}
