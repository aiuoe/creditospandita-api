<?php

use Illuminate\Database\Seeder;

class ActividadSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        
             DB::table('actividads')->insert([
                'name'=>  'Administrativas (jefes, coordinadores, asistentes, auxiliar y similares)',
                'situacionLaboralFk'=>  'Empleado',
            ]);
             DB::table('actividads')->insert([
                'name'=>  'Gerenciales (gerentes, subgerentes, directores, y similares)',
                'situacionLaboralFk'=>  'Empleado',
            ]);
             DB::table('actividads')->insert([
                'name'=>  'Operativas y de servicio (supervisores, maquinistas, operarios, analistas y similares)',
                'situacionLaboralFk'=>  'Empleado',
            ]);
             DB::table('actividads')->insert([
                'name'=>  'Ventas y Mercadeo (vendedores, agentes y similares)',
                'situacionLaboralFk'=>  'Empleado',
            ]);
             DB::table('actividads')->insert([
                'name'=>  'Técnicas (ingeniería, investigación y similares)',
                'situacionLaboralFk'=>  'Empleado',
            ]);
             DB::table('actividads')->insert([
                'name'=>  'Vigilancia y seguridad',
                'situacionLaboralFk'=>  'Empleado',
            ]);
             DB::table('actividads')->insert([
                'name'=>  'Taxista',
                'situacionLaboralFk'=>  'Empleado',
            ]);
             DB::table('actividads')->insert([
                'name'=>  'Conductor/transportador',
                'situacionLaboralFk'=>  'Empleado',
            ]);
             DB::table('actividads')->insert([
                'name'=>  'Secretariales',
                'situacionLaboralFk'=>  'Empleado',
            ]);
             DB::table('actividads')->insert([
                'name'=>  'Areas de apoyo (sistemas, contabilidad, auditoria, revosoria y similares)',
                'situacionLaboralFk'=>  'Empleado',
            ]);
             DB::table('actividads')->insert([
                'name'=>  'Policia',
                'situacionLaboralFk'=>  'Empleado',
            ]);
             DB::table('actividads')->insert([
                'name'=>  'Militar',
                'situacionLaboralFk'=>  'Empleado',
            ]);
             DB::table('actividads')->insert([
                'name'=>  'Cajero',
                'situacionLaboralFk'=>  'Empleado',
            ]);
             DB::table('actividads')->insert([
                'name'=>  'Otro',
                'situacionLaboralFk'=>  'Empleado',
            ]);
             DB::table('actividads')->insert([
                'name'=>  'Abogado',
                'situacionLaboralFk'=>  'Independiente',
            ]);
             DB::table('actividads')->insert([
                'name'=>  'actividades Agropecuarias',
                'situacionLaboralFk'=>  'Independiente',
            ]);
             DB::table('actividads')->insert([
                'name'=>  'Ama de casa',
                'situacionLaboralFk'=>  'Independiente',
            ]);
             DB::table('actividads')->insert([
                'name'=>  'Artistas',
                'situacionLaboralFk'=>  'Independiente',
            ]);
             DB::table('actividads')->insert([
                'name'=>  'Cocineros',
                'situacionLaboralFk'=>  'Independiente',
            ]);
             DB::table('actividads')->insert([
                'name'=>  'Contador/Revisor',
                'situacionLaboralFk'=>  'Independiente',
            ]);
             DB::table('actividads')->insert([
                'name'=>  'Empresario pyme',
                'situacionLaboralFk'=>  'Independiente',
            ]);
             DB::table('actividads')->insert([
                'name'=>  'Ingeniero/Geologo/Arquitectos',
                'situacionLaboralFk'=>  'Independiente',
            ]);
             DB::table('actividads')->insert([
                'name'=>  'Medico/Profesionales del sector salud/estetica',
                'situacionLaboralFk'=>  'Independiente',
            ]);
             DB::table('actividads')->insert([
                'name'=>  'Mensajero',
                'situacionLaboralFk'=>  'Independiente',
            ]);
             DB::table('actividads')->insert([
                'name'=>  'Microempresario',
                'situacionLaboralFk'=>  'Independiente',
            ]);
             DB::table('actividads')->insert([
                'name'=>  'Operarios de maquinaria',
                'situacionLaboralFk'=>  'Independiente',
            ]);
             DB::table('actividads')->insert([
                'name'=>  'Peluquero',
                'situacionLaboralFk'=>  'Independiente',
            ]);
             DB::table('actividads')->insert([
                'name'=>  'Plomero/Albañil',
                'situacionLaboralFk'=>  'Independiente',
            ]);
             DB::table('actividads')->insert([
                'name'=>  'Profesional o tecnico en informarica (software)',
                'situacionLaboralFk'=>  'Independiente',
            ]);
             DB::table('actividads')->insert([
                'name'=>  'Profesores/Docentes',
                'situacionLaboralFk'=>  'Independiente',
            ]);
             DB::table('actividads')->insert([
                'name'=>  'Secretaras',
                'situacionLaboralFk'=>  'Independiente',
            ]);
             DB::table('actividads')->insert([
                'name'=>  'Servicios de vigilancia/Policias/Militares',
                'situacionLaboralFk'=>  'Independiente',
            ]);
             DB::table('actividads')->insert([
                'name'=>  'Taxista/Transportador/Conductor',
                'situacionLaboralFk'=>  'Independiente',
            ]);
             DB::table('actividads')->insert([
                'name'=>  'Tecnicos o tecnologos en otras areas',
                'situacionLaboralFk'=>  'Independiente',
            ]);
             DB::table('actividads')->insert([
                'name'=>  'Vendedores/Visitadores',
                'situacionLaboralFk'=>  'Independiente',
            ]);
             DB::table('actividads')->insert([
                'name'=>  'Otro',
                'situacionLaboralFk'=>  'Independiente',
            ]);

   

    }
}
