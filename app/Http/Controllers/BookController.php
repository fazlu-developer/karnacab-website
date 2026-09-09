<?php

namespace App\Http\Controllers;

use App\Services\LaravelApiClient;
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
            'quote' => session('booking_quote'),
            'booking' => session('booking'),
            'payment' => session('booking_payment'),
            'trip' => session('booking_trip', []),
            'product' => old('product', $request->query('product', session('booking_trip.product', 'LOCAL_CAB'))),
            'category' => old('category', $request->query('category', session('booking_trip.category', 'SEDAN'))),
        ]);
    }

    public function quote(Request $request, LaravelApiClient $api): RedirectResponse
    {
        $data = $this->tripInput($request);
        try {
            $quote = $api->quoteRide($data);
        } catch (Throwable $exception) {
            return back()->withErrors(['quote' => 'Could not fetch a fare quote: '.$exception->getMessage()])->withInput();
        }
        $request->session()->put('booking_quote', $quote);
        $request->session()->put('booking_trip', $data);

        return back()->withInput()->with('status', 'Fare estimate is from Laravel fare rules.');
    }

    public function store(Request $request, LaravelApiClient $api): RedirectResponse
    {
        $data = $this->tripInput($request);
        $people = $request->validate([
            'passengerName' => ['required', 'string', 'max:120'],
            'passengerPhone' => ['required', 'string', 'max:20'],
            'scheduledAt' => ['nullable', 'date'],
            'returnAt' => ['nullable', 'date'],
            'flightNumber' => ['nullable', 'string', 'max:32'],
            'trainNumber' => ['nullable', 'string', 'max:32'],
            'instructions' => ['nullable', 'string', 'max:500'],
        ]);
        $payload = $data + $people;
        try {
            $booking = $api->createBooking($payload, $this->customer());
        } catch (Throwable $exception) {
            return back()->withErrors(['booking' => 'Could not create the booking: '.$exception->getMessage()])->withInput();
        }
        $request->session()->put('booking', $booking);
        $request->session()->forget('booking_payment');

        return redirect()->route('book')->with('status', 'Booking request saved. Continue to payment.');
    }

    public function pay(Request $request, LaravelApiClient $api): RedirectResponse
    {
        $booking = $request->session()->get('booking');
        abort_unless(is_array($booking) && isset($booking['id']), 422, 'Create a booking first');
        $data = $request->validate([
            'method' => ['required', 'in:upi,card,wallet,cash'],
        ]);
        try {
            $result = $api->payBooking((int) $booking['id'], $data, $this->customer());
        } catch (Throwable $exception) {
            return back()->withErrors(['payment' => 'Payment could not start: '.$exception->getMessage()]);
        }
        $request->session()->put('booking', $result['booking'] ?? $booking);
        $request->session()->put('booking_payment', $result['payment'] ?? $result);

        return redirect()->route('book.confirm', ['id' => $booking['id']]);
    }

    public function confirm(Request $request, LaravelApiClient $api, int $id): View
    {
        try {
            $booking = $api->showBooking($id, $this->customer());
        } catch (Throwable) {
            $booking = $request->session()->get('booking');
        }

        return view('pages.book-confirm', [
            'booking' => is_array($booking) ? $booking : [],
            'payment' => $request->session()->get('booking_payment'),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function tripInput(Request $request): array
    {
        $data = $request->validate([
            'product' => ['required', 'string'],
            'category' => ['required', 'string'],
            'pickupText' => ['required', 'string', 'max:255'],
            'dropText' => ['required', 'string', 'max:255'],
            'pickupLat' => ['nullable', 'numeric'],
            'pickupLng' => ['nullable', 'numeric'],
            'dropLat' => ['nullable', 'numeric'],
            'dropLng' => ['nullable', 'numeric'],
            'hours' => ['nullable', 'integer', 'min:1', 'max:24'],
        ]);
        unset($data['totalPaise'], $data['discountPaise']);

        return $data;
    }

    /**
     * @return array{email: string, name: string, phone: string}
     */
    private function customer(): array
    {
        $user = auth()->user();
        abort_unless($user, 401);

        return [
            'email' => (string) $user->email,
            'name' => (string) $user->name,
            'phone' => '',
        ];
    }
}
