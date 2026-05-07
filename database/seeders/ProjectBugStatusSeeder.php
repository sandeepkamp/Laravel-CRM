<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProjectBugStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('bug_status')->insert([
            [
                'status_name' => 'New',
                'slug' => 'new',
                'description' => 'Bug has been reported but not yet reviewed',
                'bg_color' => '#015696',
                'font_color' => '#FFF',
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],[
                'status_name' => 'Confirmed',
                'slug' => 'confirmed',
                'description' => 'Bug has been acknowledged as valid and needs fixing',
                'bg_color' => '#EEAA23',
                'font_color' => '#000',
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],[
                'status_name' => 'In Progress',
                'slug' => 'in-progress',
                'description' => 'Developer is actively working on fixing the bug',
                'bg_color' => '#012A48',
                'font_color' => '#FFF',
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],[
                'status_name' => 'Blocked',
                'slug' => 'blocked',
                'description' => 'Work on the bug is blocked due to dependencies or other issues',
                'bg_color' => '#FFBB44',
                'font_color' => '#000',
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],[
                'status_name' => 'Resolved',
                'slug' => 'resolved',
                'description' => 'A fix has been implemented, but testing is needed',
                'bg_color' => '#F8F8F8',
                'font_color' => '#000',
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],[
                'status_name' => 'Testing',
                'slug' => 'testing',
                'description' => 'The fix is being verified by QA or the reporter',
                'bg_color' => '#DDDDDD',
                'font_color' => '#000',
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],[
                'status_name' => 'Reopened',
                'slug' => 'reopened',
                'description' => 'The issue persists after being marked as resolved',
                'bg_color' => '#666666',
                'font_color' => '#000',
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],[
                'status_name' => 'Closed',
                'slug' => 'closed',
                'description' => 'Bug is fixed and verified successfully',
                'bg_color' => '#A86F00',
                'font_color' => '#000',
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],[
                'status_name' => 'Deferred',
                'slug' => 'deferred',
                'description' => 'Bug is postponed for a later release',
                'bg_color' => '#0288D1',
                'font_color' => '#000',
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],[
                'status_name' => 'Won\'t Fix',
                'slug' => 'wont-fix',
                'description' => 'The bug is acknowledged but won’t be fixed (e.g., low priority, intended behavior)',
                'bg_color' => '#000',
                'font_color' => '#FFF',
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],[
                'status_name' => 'Duplicate',
                'slug' => 'duplicate',
                'description' => 'The bug is a duplicate of an existing report',
                'bg_color' => '#ff4c51',
                'font_color' => '#FFF',
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }
}
