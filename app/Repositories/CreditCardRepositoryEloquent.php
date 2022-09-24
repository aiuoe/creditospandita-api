<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\CreditCardRepository;
use App\Models\CreditCard;
use App\Validators\CreditCardValidator;

/**
 * Class CreditCardRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class CreditCardRepositoryEloquent extends BaseRepository implements CreditCardRepository
{
    /**
      * @var array
    */
    protected $fieldSearchable = [
        'card_number'       => 'like',
        'expiration_year'   => 'like',
        'expiration_month'  => 'like',
        'cvc'               => 'like',
        'client.first_name' => 'like',
        'client.last_name'  => 'like',
        'client.email'      => 'like',
        'client.country.name'           =>  'like',
        'client.nationalities.passport' =>  'like',
        'client.nationalities.visa'     =>  'like',
    ];
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return CreditCard::class;
    }



    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

}
