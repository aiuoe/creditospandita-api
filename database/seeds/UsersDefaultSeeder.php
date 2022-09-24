<?php

use Illuminate\Database\Seeder;
use App\Repositories\UserRepositoryEloquent;

class UsersDefaultSeeder extends Seeder
{

    /**
     * @var UserRepositoryEloquent
     */
    protected $repository;

    public function __construct(UserRepositoryEloquent $repository){
        $this->repository = $repository;
    }
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = [
            [
                'first_name'=>  'Edgar',
                'second_name'=>  '',
                'last_name' =>  'Gomez',
                'second_last_name' =>  '',
                'phone_number' =>  '',
                'n_document' =>  '123456',
                'email'     =>  '1092edgar@gmail.com',
                'password'  =>  '123456'
            ]

        ];

        foreach( $users as $data )
        {
            $user = $this->repository->create( $data );
            $user->assignRole('Administrador');
            
        }
    }
}
