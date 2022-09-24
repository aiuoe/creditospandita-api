<?php

use Illuminate\Database\Seeder;
use App\Repositories\PaymentMethodRepositoryEloquent;

class PaymentMethodsDefaultSeeder extends Seeder
{
    /**
     * @var $repository PaymentMethodRepositoryEloquent
     */
    protected $repository;

    protected $data = [
        'Efectivo',
        'Tarjeta de débito',
        'Tarjeta de crédito',
        'Cheque',
        'Transferencia'
    ];

    public function __construct(PaymentMethodRepositoryEloquent $repository){
        $this->repository = $repository;
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach( $this->data as $name )
        {
            $this->repository->create(['name'=>$name]);
        }
    }
}
