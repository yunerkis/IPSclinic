<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use DB;

class ClientsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('clients')->insert([
            [
                'first_names' => 'IPS', 
                'last_names_1' => 'Salud',
                'last_names_2' => 'Antioquia',
                'dni' => '900219765-2',
                'status' => 'activo',
            ],
        ]);
    }
}
