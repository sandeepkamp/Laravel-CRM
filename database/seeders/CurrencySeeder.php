<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('currency')->insert([
            [
                'name' => 'Pound Sterling',
                'code' => 'GBP',
                'symbol' => '£',
                'created_at' => now(),
                'updated_at' => now()
            ],[
                'name' => 'US Dollars',
                'code' => 'USD',
                'symbol' => '$',
                'created_at' => now(),
                'updated_at' => now()
            ],[
                'name' => 'Indian Rupee',
                'code' => 'INR',
                'symbol' => '₹',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }
}
