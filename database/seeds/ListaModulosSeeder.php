<?php

use Illuminate\Database\Seeder;

class ListaModulosSeeder extends Seeder
{
    protected $modulos = [
        'Usuarios',
        'Blogs',
        'Evaluacion',
        'Preguntas',
        'Analisis crediticio',
        'Testimoniales',
        'Contactos',
        'Calculadoras',
        'Perfil',
        'Nueva solicitud',
        'Firmar contrato',
        'Credito',
        'Historial crediticio',
        'Informacion adicional',
        'Pagar creditos',
        'Novacion',
        'Plan de referidos'

    ];
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //

        foreach( $this->modulos as $name )
        {

            DB::table('lista_modulos')->insert([
                'nombre' => $name,
            ]);

        }
    }
}
