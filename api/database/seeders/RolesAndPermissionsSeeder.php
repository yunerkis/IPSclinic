<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // user admin
            Permission::create(['name' => 'clients.list']);
            Permission::create(['name' => 'clients.sessions.list']);
            Permission::create(['name' => 'clients.sessions.imports']);

        // Admin
        $admin = Role::create(['name' => 'admin']);

        $admin->givePermissionTo(Permission::all());

        //User admin

        $user = User::find(1);
 
        $user->assignRole('admin');
    }
}
