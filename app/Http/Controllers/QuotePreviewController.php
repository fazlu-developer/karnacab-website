<?php

namespace App\Http\Controllers;

use App\Services\LaravelApiClient;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Throwable;

class QuotePreviewController extends Controller
{
    public function store(Request $request, LaravelApiClient $api): RedirectResponse
    {
        $data = $request->validate([
            'product' => ['required', 'string'],
            'category' => ['required', 'string'],
            'distanceKm' => ['required', 'numeric', 'min:1', 'max:2000'],
            'hours' => ['nullable', 'integer', 'min:1', 'max:24'],
        ]);

        $payload = [
            'product' => $data['product'],
            'category' => $data['category'],
            'distanceKm' => (float) $data['distanceKm'],
        ];
        if (! empty($data['hours'])) {
            $payload['hours'] = (int) $data['hours'];
        }

        try {
            $quote = $api->quoteRide($payload);
        } catch (Throwable $exception) {
            return back()->withErrors(['quote' => 'Could not fetch a fare quote: '.$exception->getMessage()])->withInput();
        }

        return back()->with('quote', $quote)->withInput();
    }
}
