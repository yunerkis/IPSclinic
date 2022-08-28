<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class NewRoleAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Permission::create(['name' => 'doctor.user']);
        Permission::create(['name' => 'clients.result']);
        Permission::create(['name' => 'users.list']);
        Permission::create(['name' => 'users.destroy']);
        Permission::create(['name' => 'clients.result.destroy']);

        $doctor = Role::create(['name' => 'doctor']);

        $admin = Role::where('name', 'admin')->first();
        $admin->givePermissionTo(Permission::all());

        $doctor->givePermissionTo(['clients.result', 'clients.list', 'clients.result.destroy']);
    }
}
