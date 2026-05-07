<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departments = [
            'Administration',
            'Development',
            'Sales & Marketing',
            'Human Resources',
            'Freelance'
        ];

        foreach($departments as $department) {
            Department::firstOrCreate(['name' => $department]);
        }
    }
}
