<?php



namespace Database\Seeders;



use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use Illuminate\Database\Seeder;

use Illuminate\Support\Facades\DB;



class UserRoleSeeder extends Seeder

{

    /**
     * Run the database seeds.
     * 'Administration' => 1,   'Development' => 2,   'Sales & Marketing' => 3,   'Human Resources' => 4,   'Freelance' => 5
     */

    public function run(): void

    {

        DB::table('user_role')->insert([

            [

                'name' => 'Admin',

                'status' => 1,
                'department_id' => 1,

                'created_at' => now(),

                'updated_at' => now()

            ],[

                'name' => 'Sales',

                'status' => 1,
                'department_id' => 3,

                'created_at' => now(),

                'updated_at' => now()

            ],[

                'name' => 'HR',

                'status' => 1,
                'department_id' => 4,

                'created_at' => now(),

                'updated_at' => now()

            ],[

                'name' => 'Developer',

                'status' => 1,
                'department_id' => 2,

                'created_at' => now(),

                'updated_at' => now()

            ],[

                'name' => 'SEO',

                'status' => 1,
                'department_id' => 3,

                'created_at' => now(),

                'updated_at' => now()

            ],[

                'name' => 'Freelancer',

                'status' => 1,
                'department_id' => 5,

                'created_at' => now(),

                'updated_at' => now()

            ],[

                'name' => 'Project Manager',

                'status' => 1,
                'department_id' => 2,

                'created_at' => now(),

                'updated_at' => now()

            ],[

                'name' => 'Team Leader',

                'status' => 1,
                'department_id' => 2,

                'created_at' => now(),

                'updated_at' => now()

            ]

        ]);

    }

}

