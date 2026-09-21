<?php

namespace App\Http\Controllers;

use App\Services\LaravelApiClient;
use App\Services\TripFareService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Throwable;

class BookController extends Controller
{
    public function show(Request $request, LaravelApiClient $api): View
    {
        $catalog = [];
        try {
            $catalog = $api->rideCatalog();
        } catch (Throwable) {
            $catalog = [];
        }

        return view('pages.book', [
            'catalog' => $catalog,
            'quotes' => session('booking_quotes', []),
            'trip' => session('booking_trip', []),
            'product' => old('product', $request->query('product', session('booking_trip.product', 'LOCAL_CAB'))),
            'downloadNotice' => (bool) session('download_notice'),
        ]);
    }

    public function quote(Request $request, TripFareService $fares): RedirectResponse
    {
        $data = $this->tripInput($request);
        $quotes = $fares->quotes($data);
        $request->session()->put('booking_quotes', $quotes['vehicles']);
        $request->session()->put('booking_trip', $data + ['distanceKm' => $quotes['distanceKm']]);
        if (! empty($quotes['comingSoon'])) {
            return back()->withInput()->withErrors(['quote' => $quotes['message'] ?? 'Coming soon in this state.']);
        }

        return back()->withInput()->with('status', 'Choose a vehicle. Booking continues in the KarnaCab customer app.');
    }

    public function continue(Request $request): RedirectResponse
    {
        $request->validate([
            'category' => ['nullable', 'string', 'max:40'],
        ]);
        $request->session()->flash('download_notice', true);

        return redirect()->route('download', array_filter([
            'category' => $request->input('category'),
        ]))->with('status', 'Please download the KarnaCab customer app to complete this booking.');
    }

    /**
     * @return array<string, mixed>
     */
    private function tripInput(Request $request): array
    {
        return $request->validate([
            'product' => ['required', 'string'],
            'pickupText' => ['required', 'string', 'max:255'],
            'dropText' => ['required', 'string', 'max:255'],
            'pickupLat' => ['nullable', 'numeric'],
            'pickupLng' => ['nullable', 'numeric'],
            'dropLat' => ['nullable', 'numeric'],
            'dropLng' => ['nullable', 'numeric'],
            'hours' => ['nullable', 'integer', 'min:1', 'max:24'],
        ]);
    }
}
