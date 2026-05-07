<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProjectTaskStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('task_status')->insert([
            [
                'status_name' => 'Pending',
                'slug' => 'pending',
                'description' => 'Task is created but not yet reviewed',
                'bg_color' => '#015696',
                'font_color' => '#FFF',
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],[
                'status_name' => 'To Do',
                'slug' => 'to-do',
                'description' => 'Task is approved but not started',
                'bg_color' => '#EEAA23',
                'font_color' => '#FFF',
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],[
                'status_name' => 'Blocked',
                'slug' => 'blocked',
                'description' => 'Task cannot proceed due to dependencies/issues',
                'bg_color' => '#012A48',
                'font_color' => '#FFF',
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],[
                'status_name' => 'In Progress',
                'slug' => 'in-progress',
                'description' => 'Task is actively being worked on',
                'bg_color' => '#FFBB44',
                'font_color' => '#000',
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],[
                'status_name' => 'Under Review',
                'slug' => 'under-review',
                'description' => 'Work is done and awaiting peer or lead review',
                'bg_color' => '#F8F8F8',
                'font_color' => '#000',
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],[
                'status_name' => 'Revisions Needed',
                'slug' => 'revisions-needed',
                'description' => 'Changes or improvements are required before approval',
                'bg_color' => '#DDDDDD',
                'font_color' => '#000',
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],[
                'status_name' => 'Testing',
                'slug' => 'testing',
                'description' => 'Task is being tested (QA or user testing)',
                'bg_color' => '#666666',
                'font_color' => '#000',
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],[
                'status_name' => 'Ready for Deployment',
                'slug' => 'ready-for-deployment',
                'description' => 'Task is approved and awaiting release',
                'bg_color' => '#9055fd',
                'font_color' => '#FFF',
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],[
                'status_name' => 'Completed',
                'slug' => 'completed',
                'description' => 'Task is finished and verified',
                'bg_color' => '#A86F00',
                'font_color' => '#000',
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],[
                'status_name' => 'Closed',
                'slug' => 'closed',
                'description' => 'Task is finalized and archived',
                'bg_color' => '#0288D1',
                'font_color' => '#FFF',
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],[
                'status_name' => 'Deferred',
                'slug' => 'deferred',
                'description' => 'Task is postponed for a later release',
                'bg_color' => '#000',
                'font_color' => '#FFF',
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],[
                'status_name' => 'Canceled',
                'slug' => 'canceled',
                'description' => 'Task is no longer needed or relevant',
                'bg_color' => '#ff4c51',
                'font_color' => '#FFF',
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }
}
