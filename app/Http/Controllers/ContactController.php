<?php

namespace App\Http\Controllers;

use App\Services\NestApiClient;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Throwable;

class ContactController extends Controller
{
    public function store(Request $request, NestApiClient $api): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'phone' => ['required', 'string', 'max:20'],
            'email' => ['required', 'email', 'max:180'],
            'message' => ['required', 'string', 'min:8', 'max:2000'],
            'district' => ['nullable', 'string', 'max:80'],
        ]);

        try {
            $api->createLead([
                'type' => 'SUPPORT',
                'name' => $data['name'],
                'phone' => $data['phone'],
                'email' => $data['email'],
                'district' => $data['district'] ?? null,
                'message' => $data['message'],
            ]);
        } catch (Throwable $exception) {
            return back()->withErrors(['api' => 'Could not reach the KarnaCab API: '.$exception->getMessage()])->withInput();
        }

        return back()->with('status', 'Thanks. We received your message and will get back to you.');
    }
}
