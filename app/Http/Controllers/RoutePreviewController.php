<?php

namespace App\Http\Controllers;

use App\Services\GooglePlacesService;
use App\Services\TripFareService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Throwable;

class RoutePreviewController extends Controller
{
    public function __invoke(Request $request, GooglePlacesService $places, TripFareService $fares): View
    {
        $pickup = trim((string) $request->query('pickup', ''));
        $drop = trim((string) $request->query('drop', ''));
        $vehicle = $request->query('vehicle', 'bike');
        if (! in_array($vehicle, ['bike', 'auto', 'cab'], true)) {
            $vehicle = 'bike';
        }

        $result = [
            'pickup' => $pickup,
            'drop' => $drop,
            'vehicle' => $vehicle,
            'from' => null,
            'to' => null,
            'geometry' => null,
            'distance_km' => null,
            'duration_min' => null,
            'error' => null,
            'quotes' => [],
        ];

        if ($pickup === '' && $drop === '') {
            return view('pages.route', $result);
        }

        $validated = $request->validate([
            'pickup' => ['required', 'string', 'max:255'],
            'drop' => ['required', 'string', 'max:255'],
            'vehicle' => ['nullable', 'in:bike,auto,cab'],
            'pickup_lat' => ['nullable', 'numeric'],
            'pickup_lng' => ['nullable', 'numeric'],
            'drop_lat' => ['nullable', 'numeric'],
            'drop_lng' => ['nullable', 'numeric'],
        ]);

        try {
            $from = $this->resolvePoint($places, $validated['pickup'], $validated['pickup_lat'] ?? null, $validated['pickup_lng'] ?? null);
            $to = $this->resolvePoint($places, $validated['drop'], $validated['drop_lat'] ?? null, $validated['drop_lng'] ?? null);
        } catch (Throwable) {
            $result['error'] = 'Could not look up those places right now. Try again in a moment.';

            return view('pages.route', $result);
        }

        if ($from === null || $to === null) {
            $result['error'] = 'We could not find one of those locations. Pick a Google suggestion or try a fuller address.';

            return view('pages.route', $result);
        }

        $result['from'] = $from;
        $result['to'] = $to;

        try {
            $route = $places->directions($from['lat'], $from['lng'], $to['lat'], $to['lng']);
        } catch (Throwable) {
            $result['error'] = 'Places found, but the road route could not be loaded. Pins are shown on the map.';

            return view('pages.route', $result);
        }

        $result['distance_km'] = $route['distanceKm'] ?? null;
        $result['duration_min'] = $route['durationMinutes'] ?? null;
        $quotes = $fares->quotes([
            'product' => 'LOCAL_CAB',
            'distanceKm' => $result['distance_km'],
            'pickupLat' => $from['lat'],
            'pickupLng' => $from['lng'],
            'dropLat' => $to['lat'],
            'dropLng' => $to['lng'],
            'pickupText' => $from['label'] ?? $pickup,
            'dropText' => $to['label'] ?? $drop,
        ]);
        $result['quotes'] = $quotes['vehicles'];
        if (! empty($quotes['comingSoon'])) {
            $result['error'] = $quotes['message'] ?? 'Coming soon in this state.';
        }

        return view('pages.route', $result);
    }

    /**
     * @return array{label: string, lat: float, lng: float}|null
     */
    private function resolvePoint(GooglePlacesService $places, string $query, mixed $lat, mixed $lng): ?array
    {
        if ($lat !== null && $lng !== null && $lat !== '' && $lng !== '') {
            return ['label' => $query, 'lat' => (float) $lat, 'lng' => (float) $lng];
        }
        $first = $places->autocomplete($query)[0] ?? null;
        if (! is_array($first) || empty($first['placeId'])) {
            return null;
        }
        $place = $places->details((string) $first['placeId']);
        if (empty($place['lat']) && empty($place['lng'])) {
            return null;
        }

        return [
            'label' => (string) ($place['address'] ?? $query),
            'lat' => (float) $place['lat'],
            'lng' => (float) $place['lng'],
        ];
    }
}
