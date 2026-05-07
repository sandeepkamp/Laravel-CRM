<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            'first_name' => 'Digitex',
            'last_name' => 'Admin',
            'email' => 'saubhik@digitextechnologies.com',
            'password' => Hash::make('secret'),
            'date_registered' => now(),
            'status' => 1,
            'role' => 1,
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
}
