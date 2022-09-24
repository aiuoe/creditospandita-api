<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Repositories\PassengerRepositoryEloquent;
use App\Notifications\PassengerHappyBirthday;
use DB;

class HappyBirthday extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'passengers:happy_birthday';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Correo de feliz cumpleaños a los viajeros/pasajeros';

    protected $passengerRepository;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct( PassengerRepositoryEloquent $passengerRepository )
    {
        parent::__construct();
        $this->passengerRepository = $passengerRepository;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $passengers = $this->passengerRepository->findWhere(
            [
                [
                    db::raw("MONTH(birthdate)"),    '=',    date('m'),
                ],
                [
                    db::raw("DAY(birthdate)"),    '=',    date('d'),
                ]
            ]
        );

        foreach($passengers as $passenger)
        {
            $passenger->notify( new PassengerHappyBirthday() );
        }
    }
}
