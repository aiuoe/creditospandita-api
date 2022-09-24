<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;


class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       $permissions = [
            'user-list',
            'user-store',
            'user-update',
            'user-destroy',
            'office-list',
            'office-store',
            'office-update',
            'office-destroy',
            'passenger-list',
            'passenger-store',
            'passenger-update',
            'passenger-destroy',
            'collaborator-list',
            'collaborator-store',
            'collaborator-update',
            'collaborator-destroy',
            'vacation-record-list',
            'vacation-record-store',
            'vacation-record-update',
            'vacation-record-destroy',
            'task-list',
            'task-store',
            'task-update',
            'task-destroy',
            'payment-list',
            'payment-store',
            'payment-update',
            'payment-destroy',
            'service-list',
            'service-store',
            'service-update',
            'service-destroy',
            'prospect-list',
            'prospect-store',
            'prospect-update',
            'prospect-destroy',
            'client-list',
            'client-store',
            'client-update',
            'client-destroy',
            'propoal-list',
            'propoal-store',
            'propoal-update',
            'propoal-destroy',
        ];


        foreach ($permissions as $permission) {
             Permission::create(['name' => $permission]);
        }
    }
}
