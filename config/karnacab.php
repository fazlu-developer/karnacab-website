<?php

return [
    'api' => [
        'base_url' => rtrim((string) env('NEST_API_URL', 'http://127.0.0.1:8003/api/v1'), '/'),
        'timeout' => (int) env('NEST_API_TIMEOUT', 10),
    ],
    'laravel' => [
        'base_url' => rtrim((string) env('LARAVEL_API_URL', 'http://127.0.0.1:8001/api/v1'), '/'),
        'timeout' => (int) env('LARAVEL_API_TIMEOUT', 12),
        'booking_secret' => env('WEBSITE_BOOKING_SECRET', 'karnacab-dev-website'),
    ],
    'google_maps_key' => (string) env('GOOGLE_MAPS_API', ''),
    'maps' => [
        'nominatim_url' => rtrim((string) env('OSM_NOMINATIM_URL', 'https://nominatim.openstreetmap.org'), '/'),
        'osrm_url' => rtrim((string) env('OSRM_URL', 'https://router.project-osrm.org'), '/'),
        'timeout' => (int) env('MAPS_TIMEOUT', 8),
    ],
    'admin_url' => rtrim((string) env('ADMIN_PUBLIC_URL', 'https://admin.karnacab.in'), '/'),
    'leads_notify_email' => (string) env('LEADS_NOTIFY_EMAIL', env('MAIL_FROM_ADDRESS', 'website.fazlu@gmail.com')),
    'default_catalog' => [
        'rideTypes' => [
            ['key' => 'LOCAL_CAB', 'title' => 'Local Cab'],
            ['key' => 'ONE_WAY', 'title' => 'One Way'],
            ['key' => 'ROUND_WAY', 'title' => 'Round Way'],
            ['key' => 'RENTAL', 'title' => 'Rental'],
            ['key' => 'SCHEDULE', 'title' => 'Schedule'],
            ['key' => 'OUTSTATION', 'title' => 'Outstation'],
            ['key' => 'AIRPORT', 'title' => 'Airport'],
            ['key' => 'RAILWAY', 'title' => 'Railway'],
            ['key' => 'MULTI_STOP', 'title' => 'Multi-stop'],
        ],
        'vehicleTypes' => [
            ['key' => 'BIKE', 'title' => 'Bike'],
            ['key' => 'AUTO', 'title' => 'Auto'],
            ['key' => 'E_RICKSHAW', 'title' => 'E-Rickshaw'],
            ['key' => 'MINI', 'title' => 'Mini'],
            ['key' => 'SEDAN', 'title' => 'Sedan'],
            ['key' => 'SUV', 'title' => 'SUV'],
            ['key' => 'TRAVELLER', 'title' => 'Traveller'],
        ],
    ],
];
