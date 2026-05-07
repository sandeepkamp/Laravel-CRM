<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Attendance;
use App\Models\User;
use Illuminate\Support\Carbon;

class AttendanceSeeder extends Seeder
{
    public function run(): void
    {
        $employees = [];
        $employeeIds = User::pluck('id')->toArray();

        // Generate 5 dummy employees
        for ($i = 1; $i <= 5; $i++) {
            $employees[] = [
                'code' => 'EMP' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'name' => 'Employee ' . $i,
            ];
        }

        $monthsWithDays = [
            2 => 15, // February
            3 => 15, // March
            4 => 15, // April
            5 => 10, // May
        ];
        $year = now()->year;

        // Collect weekday dates per month
        $datesByMonth = [];

        // Collect weekday dates per month
        foreach ($monthsWithDays as $month => $maxDays) {
            $date = Carbon::createFromDate($year, $month, 1);
            $dates = [];

            while ($date->month === $month && count($dates) < $maxDays) {
                if ($date->isWeekday()) {
                    $dates[] = $date->copy();
                }
                $date->addDay();
            }

            $datesByMonth[$month] = $dates;
        }

        // Determine the maximum number of days across all months
        $maxDays = max(array_map('count', $datesByMonth));

        // Interleave by date index across months
        for ($i = 0; $i < $maxDays; $i++) {
            foreach ($monthsWithDays as $month => $dayCount) {
                if (!isset($datesByMonth[$month][$i])) continue;

                $day = $datesByMonth[$month][$i];

                foreach ($employees as $employee) {
                    // 15% absent chance
                    if (rand(1, 100) <= 15) continue;

                    $isLate = rand(1, 100) <= 20;
                    $inHour = $isLate ? 11 : 10;
                    $inMinute = rand(0, 59);
                    $punchIn = Carbon::createFromTime($inHour, $inMinute);
                    // $punchOut = (clone $punchIn)->addHours(rand(8, 9))->addMinutes(rand(0, 59));
                    $punchOut = Carbon::createFromTime(20, $inMinute);

                    // Lunch: between 2 PM to 4 PM
                    $lunchStartHour = rand(14, 15); // 14 = 2 PM, 15 = 3 PM
                    $lunchStartMinute = rand(0, 29);
                    $lunchStart = Carbon::createFromTime($lunchStartHour, $lunchStartMinute);
                    $lunchEnd = (clone $lunchStart)->addMinutes(30);

                    // Break: between 4 PM to 7 PM
                    $breakStartHour = rand(16, 18); // 16 = 4 PM, 18 = 6 PM
                    $breakStartMinute = rand(0, 29);
                    $breakStart = Carbon::createFromTime($breakStartHour, $breakStartMinute);
                    $breakEnd = (clone $breakStart)->addMinutes(30);

                    $employeeId = $employeeIds[array_rand($employeeIds)];

                    Attendance::create([
                        'employee_id' => $employeeId,
                        'employee_code' => 'digitex_' . rand(100000, 999999),
                        'employee_name' => $employee['name'],
                        'date'          => $day->toDateString(),
                        'punch_in'      => $punchIn->format('H:i:s'),
                        'punch_out'     => $punchOut->format('H:i:s'),
                        'lunch_start'   => $lunchStart->format('H:i:s'),
                        'lunch_end'     => $lunchEnd->format('H:i:s'),
                        'break_start'   => $breakStart->format('H:i:s'),
                        'break_end'     => $breakEnd->format('H:i:s'),
                    ]);
                }
            }
        }

    }
}
