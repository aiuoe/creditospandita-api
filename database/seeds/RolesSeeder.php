<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = [
            'Administrador'     =>  Permission::all()->pluck('name')->toArray(),
            'Cliente'     =>  Permission::all()->pluck('name')->toArray(),
            'Analista'     =>  Permission::all()->pluck('name')->toArray(),
        ];

        foreach( $roles as $name => $permissions )
        {

            $role = Role::updateOrCreate(['name' => $name],['name' => $name]);
            foreach( $permissions as $permission )
            {
                $role->givePermissionTo($permission);
            }

        }


    }
}
