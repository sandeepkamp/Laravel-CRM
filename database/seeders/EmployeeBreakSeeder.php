<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\EmployeeBreak;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Str;

class EmployeeBreakSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $employeeIds = User::pluck('id')->toArray();
        $reasons = ['Tea Break', 'Personal Call', 'Restroom', 'Quick Walk', 'Refreshment'];
        $date = now()->toDateString();

        foreach ($employeeIds as $employeeId) {
            $totalMinutes = 0;

            while ($totalMinutes < 60) {
                $duration = rand(5, 20);
                if ($totalMinutes + $duration > 60) break;

                $startTime = Carbon::createFromTime(rand(9, 15), rand(0, 59), 0);
                $endTime = (clone $startTime)->addMinutes($duration);

                EmployeeBreak::create([
                    'employee_id' => $employeeId,
                    'date'        => $date,
                    'start_time'  => $startTime->format('H:i:s'),
                    'end_time'    => $endTime->format('H:i:s'),
                    'duration'    => $duration,
                    'reason'      => $reasons[array_rand($reasons)],
                ]);

                $totalMinutes += $duration;
            }
        }
    }
}
