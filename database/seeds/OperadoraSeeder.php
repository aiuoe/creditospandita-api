<?php

use Illuminate\Database\Seeder;
use App\Models\Operadora;

class OperadoraSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('operadoras')->insert([
            'operadora' =>	'Tigo',
            'prefijo' =>	'300'
        ]);
        DB::table('operadoras')->insert([
            'operadora' =>	'Tigo',
            'prefijo' =>	'300'
        ]);
        DB::table('operadoras')->insert([
            'operadora' =>	'Tigo',
            'prefijo' =>	'301'
        ]);
        DB::table('operadoras')->insert([
            'operadora' =>	'Tigo',
            'prefijo' =>	'302'
        ]);
        DB::table('operadoras')->insert([
            'operadora' =>	'Uff Movil',
            'prefijo' =>	'303'
        ]);
        DB::table('operadoras')->insert([
            'operadora' =>	'Tigo',
            'prefijo' =>	'304'
        ]);
        DB::table('operadoras')->insert([
            'operadora' =>	'Tigo',
            'prefijo' =>	'305'
        ]);
        DB::table('operadoras')->insert([
            'operadora' =>	'Claro',
            'prefijo' =>	'310'
        ]);
        DB::table('operadoras')->insert([
            'operadora' =>	'Claro',
            'prefijo' =>	'311'
        ]);
        DB::table('operadoras')->insert([
            'operadora' =>	'Claro',
            'prefijo' =>	'312'
        ]);
        DB::table('operadoras')->insert([
            'operadora' =>	'Claro',
            'prefijo' =>	'313'
        ]);
        DB::table('operadoras')->insert([
            'operadora' =>	'Claro',
            'prefijo' =>	'314'
        ]);
        DB::table('operadoras')->insert([
            'operadora' =>	'Claro',
            'prefijo' =>	'320'
        ]);
        DB::table('operadoras')->insert([
            'operadora' =>	'Claro',
            'prefijo' =>	'321'
        ]);
        DB::table('operadoras')->insert([
            'operadora' =>	'Claro',
            'prefijo' =>	'322'
        ]);
        DB::table('operadoras')->insert([
            'operadora' =>	'Claro',
            'prefijo' =>	'323'
        ]);
        DB::table('operadoras')->insert([
            'operadora' =>	'Movistar',
            'prefijo' =>	'315'
        ]);
        DB::table('operadoras')->insert([
            'operadora' =>	'Movistar',
            'prefijo' =>	'316'
        ]);
        DB::table('operadoras')->insert([
            'operadora' =>	'Movistar',
            'prefijo' =>	'317'
        ]);
        DB::table('operadoras')->insert([
            'operadora' =>	'Movistar',
            'prefijo' =>	'318'
        ]);
        DB::table('operadoras')->insert([
            'operadora' =>	'Movistar',
            'prefijo' =>	'319'
        ]);
        DB::table('operadoras')->insert([
            'operadora' =>	'Movistar',
            'prefijo' =>	'350'
        ]);
        DB::table('operadoras')->insert([
            'operadora' =>	'Movistar',
            'prefijo' =>	'351'
        ]);
    }
}
