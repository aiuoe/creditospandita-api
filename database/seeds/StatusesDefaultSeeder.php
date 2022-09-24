<?php

use Illuminate\Database\Seeder;
use App\Repositories\StatusRepositoryEloquent;

class StatusesDefaultSeeder extends Seeder
{
    /**
     * @var $repository StatusRepositoryEloquent
     */
    protected $repository;

    protected $data = [
        'general'     =>    [
            'Activo',
            'Inactivo'
        ],
    ];

    public function __construct(StatusRepositoryEloquent $repository){
        $this->repository = $repository;
    }
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach( $this->data as $type => $statuses )
        {
            foreach( $statuses as $name )
            {
                $this->repository->updateOrCreate(
                [
                    'name'      =>  $name,
                    'type'      =>  $type
                ],
                [
                    'name'      =>  $name,
                    'type'      =>  $type
                ]);
            }
        }
    }
}
