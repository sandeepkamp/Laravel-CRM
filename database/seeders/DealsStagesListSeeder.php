<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DealsStagesListSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('deals_stages_list')->insert([
            [
                'name' => 'Qualified',
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],[
                'name' => 'Contact Made',
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],[
                'name' => 'Demo Scheduled',
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],[
                'name' => 'Proposal Made',
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],[
                'name' => 'Negotiations Started',
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }
}
