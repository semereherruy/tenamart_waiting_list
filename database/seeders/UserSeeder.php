<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\User::create([
            'name' => 'Semere',
            'email' => 'semere@example.com',
            'password' => bcrypt('passme'),
        ]);
    }

}
