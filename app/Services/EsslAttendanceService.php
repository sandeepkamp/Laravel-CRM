<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class EsslAttendanceService
{
    // protected $baseUrl = env('ESSL_BASE_URL');
    // protected $appKey = env('ESSL_APP_KEY'); 

    // public function getAttendanceBetweenDates($startDate, $endDate)
    // {
    //     $response = Http::get($this->baseUrl . 'GetAttendanceBetweenDates', [
    //         'AppKey' => $this->appKey,
    //         'StartDate' => $startDate,
    //         'EndDate' => $endDate,
    //     ]);

    //     if ($response->successful()) {
    //         return $response->json();
    //     }

    //     return null;
    // }
}
