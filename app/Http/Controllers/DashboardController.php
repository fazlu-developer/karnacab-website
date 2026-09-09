<?php

namespace App\Http\Controllers;

use App\Services\LaravelApiClient;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(LaravelApiClient $api): View
    {
        return view('dashboard', [
            'apiBaseUrl' => $api->baseUrl(),
            'booking' => session('booking'),
        ]);
    }
}
