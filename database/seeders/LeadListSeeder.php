<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LeadListSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('lead_label_list')->insert([
            [
                'name' => 'NEW PROSPECT',
                'status' => 1,
                'bg_color' => '#ffad58',
                'font_color' => '#000',
                'created_at' => now(),
                'updated_at' => now()
            ],[
                'name' => 'FIRST CALL COMPLETE',
                'status' => 1,
                'bg_color' => '#4164d4',
                'font_color' => '#FFF',
                'created_at' => now(),
                'updated_at' => now()
            ],[
                'name' => 'WARM PROSPECT',
                'status' => 1,
                'bg_color' => '#d63033',
                'font_color' => '#FFF',
                'created_at' => now(),
                'updated_at' => now()
            ],[
                'name' => 'COLD PROSPECT (DO NOT CALL)',
                'status' => 1,
                'bg_color' => '#fae021',
                'font_color' => '#000',
                'created_at' => now(),
                'updated_at' => now()
            ],[
                'name' => 'COLD PROSPECT (CALL IN 3-6 MONTHS)',
                'status' => 1,
                'bg_color' => '#9a592b',
                'font_color' => '#FFF',
                'created_at' => now(),
                'updated_at' => now()
            ],[
                'name' => 'EXISTING CLIENT',
                'status' => 1,
                'bg_color' => '#f88fbe',
                'font_color' => '#000',
                'created_at' => now(),
                'updated_at' => now()
            ],[
                'name' => 'PAST CLIENT',
                'status' => 1,
                'bg_color' => '#a0419d',
                'font_color' => '#FFF',
                'created_at' => now(),
                'updated_at' => now()
            ],[
                'name' => 'PROPOSAL SENT',
                'status' => 1,
                'bg_color' => '#7fd6b6',
                'font_color' => '#000',
                'created_at' => now(),
                'updated_at' => now()
            ],[
                'name' => 'SEO AUDIT SENT',
                'status' => 1,
                'bg_color' => '#e4e5e5',
                'font_color' => '#000',
                'created_at' => now(),
                'updated_at' => now()
            ],[
                'name' => 'GENERIC INITIAL EMAIL SENT',
                'status' => 1,
                'bg_color' => '#6b6e6f',
                'font_color' => '#FFF',
                'created_at' => now(),
                'updated_at' => now()
            ],[
                'name' => 'FOLLOW UP EMAIL SENT',
                'status' => 1,
                'bg_color' => '#6b6e6f',
                'font_color' => '#FFF',
                'created_at' => now(),
                'updated_at' => now()
            ],[
                'name' => 'RIA OLD LEAD',
                'status' => 1,
                'bg_color' => '#d63033',
                'font_color' => '#FFF',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }
}
