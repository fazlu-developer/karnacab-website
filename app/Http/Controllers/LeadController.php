<?php

namespace App\Http\Controllers;

use App\Services\NestApiClient;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Throwable;

class LeadController extends Controller
{
    public function store(Request $request, NestApiClient $api): RedirectResponse
    {
        $data = $request->validate([
            'type' => ['required', 'string'],
            'name' => ['required', 'string', 'max:120'],
            'phone' => ['required', 'string', 'max:20'],
            'email' => ['nullable', 'email'],
            'district' => ['nullable', 'string', 'max:80'],
            'message' => ['required', 'string', 'min:8', 'max:2000'],
            'payload' => ['nullable', 'string', 'max:4000'],
        ]);

        try {
            $api->createLead($data);
        } catch (Throwable $exception) {
            return back()->withErrors(['api' => 'Could not reach the KarnaCab API: '.$exception->getMessage()])->withInput();
        }

        return back()->with('status', 'Request received. Our Bihar team will follow up.');
    }
}
