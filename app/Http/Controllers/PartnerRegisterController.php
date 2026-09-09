<?php

namespace App\Http\Controllers;

use App\Services\NestApiClient;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;
use Throwable;

class PartnerRegisterController extends Controller
{
    public function store(Request $request, NestApiClient $api): RedirectResponse
    {
        $kind = $request->validate([
            'kind' => ['required', 'in:driver,advertiser'],
        ])['kind'];

        if ($kind === 'driver') {
            $data = $request->validate([
                'name' => ['required', 'string', 'max:120'],
                'email' => ['required', 'email'],
                'phone' => ['nullable', 'string', 'max:20'],
                'password' => ['required', 'confirmed', Password::defaults()],
                'licenseNo' => ['required', 'string', 'min:6', 'max:40'],
            ]);
            try {
                $api->registerDriver([
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'phone' => $data['phone'] ?? null,
                    'password' => $data['password'],
                    'licenseNo' => $data['licenseNo'],
                ]);
            } catch (Throwable $exception) {
                return back()->withErrors(['api' => 'Could not register driver: '.$exception->getMessage()])->withInput();
            }

            return back()->with('status', 'Driver account created. Complete KYC in the KarnaCab driver app.');
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email'],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);
        try {
            $api->registerAdvertiser([
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'password' => $data['password'],
            ]);
        } catch (Throwable $exception) {
            return back()->withErrors(['api' => 'Could not register advertiser: '.$exception->getMessage()])->withInput();
        }

        return back()->with('status', 'Advertiser account created. Sign in on the ads console to run campaigns.');
    }
}
