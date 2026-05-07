<?php



namespace Database\Seeders;



// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use Illuminate\Database\Seeder;



class DatabaseSeeder extends Seeder

{

    /**

     * Seed the application's database.

     */

    public function run(): void

    {

        $this->call([

            DepartmentSeeder::class,
            UserRoleSeeder::class,

            UserSeeder::class,

            CurrencySeeder::class,

            LeadListSeeder::class,

            DealsStagesListSeeder::class,

            HostingProviderSeeder::class,

            ProjectTaskStatusSeeder::class,

            ProjectBugStatusSeeder::class

        ]);

    }

}

