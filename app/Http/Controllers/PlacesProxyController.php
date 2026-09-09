<?php

namespace App\Http\Controllers;

use App\Services\LaravelApiClient;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class PlacesProxyController extends Controller
{
    public function suggest(Request $request, LaravelApiClient $api): JsonResponse
    {
        $data = $request->validate([
            'q' => ['required', 'string', 'min:2', 'max:180'],
        ]);
        try {
            return response()->json($api->placeAutocomplete($data['q']));
        } catch (Throwable $exception) {
            return response()->json(['predictions' => [], 'error' => $exception->getMessage()], 503);
        }
    }

    public function details(Request $request, LaravelApiClient $api): JsonResponse
    {
        $data = $request->validate([
            'placeId' => ['required', 'string', 'max:255'],
        ]);
        try {
            return response()->json($api->placeDetails($data['placeId']));
        } catch (Throwable $exception) {
            return response()->json(['error' => $exception->getMessage()], 503);
        }
    }
}
