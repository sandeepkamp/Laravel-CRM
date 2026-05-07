<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HostingProviderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('hosting_provider')->insert([
            [
                'provider_name' => 'eUKhost LTD',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'provider_name' => 'Webhosting UK COM Ltd',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
