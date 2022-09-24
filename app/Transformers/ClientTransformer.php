<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\Client;

/**
 * Class ClientTransformer.
 *
 * @package namespace App\Transformers;
 */
class ClientTransformer extends TransformerAbstract
{
    /**
     * Transform the Client entity.
     *
     * @param \App\Models\Client $model
     *
     * @return array
     */
    public function transform(Client $model)
    {

        return [
            'id'            =>  (int) $model->id,
            'name'          =>  $model->name,
            'email'         =>  $model->email,
            'phone_1'       =>  $model->phone_1,
            'phone_2'       =>  $model->phone_2,
            'birthday'      =>  $model->birthday,
            'country'       =>  $model->country,
            'city'          =>  $model->city,
            'street'        =>  $model->stream_get_contents,
            'colony'        =>  $model->colony,
            'zip_code'      =>  $model->zip_code,
            'observation'   =>  $model->observation,
            'credit_cards'  =>  $model->credit_cards,
            'payment_method'=>  $model->payment_method,
            'balance'       =>  (float) ($model->quotations->sum('total') - $model->payments->sum('import')),
            'created_at'    =>  $model->created_at->format('d-m-Y h:i:s H'),
            'updated_at'    =>  $model->updated_at->format('d-m-Y h:i:s H'),
            'date_client'   =>  (!empty($model->date_client)) ? $model->date_client->format('d-m-Y h:i:s H') : '',
            'date_prospect' =>  (!empty($model->date_prospect)) ? $model->date_prospect->format('d-m-Y h:i:s H') : '',
        ];
    }
}
