<?php

namespace App\Http\Controllers;

use App\Services\NestApiClient;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Throwable;

class DeleteAccountController extends Controller
{
    public function show(): View
    {
        return view('pages.delete-account');
    }

    public function requestOtp(Request $request, NestApiClient $api): RedirectResponse
    {
        $data = $request->validate([
            'phone' => ['required', 'string', 'max:20'],
        ]);
        try {
            $api->requestOtp($data['phone']);
        } catch (Throwable $exception) {
            return back()->withInput()->withErrors(['phone' => $this->apiMessage($exception, 'Could not send OTP. Try again.')]);
        }

        return back()->withInput()->with('status', 'OTP sent to your mobile. Enter it below to permanently delete the account.');
    }

    public function destroy(Request $request, NestApiClient $api): RedirectResponse
    {
        $data = $request->validate([
            'phone' => ['required', 'string', 'max:20'],
            'code' => ['required', 'string', 'max:8'],
        ]);
        try {
            $api->deleteCustomerAccount($data['phone'], $data['code']);
        } catch (Throwable $exception) {
            return back()->withInput()->withErrors(['code' => $this->apiMessage($exception, 'Could not delete the account.')]);
        }

        return redirect()->route('delete-account')->with('status', 'Your KarnaRide customer account and associated personal data have been deleted.');
    }

    private function apiMessage(Throwable $exception, string $fallback): string
    {
        $message = $exception->getMessage();
        if (preg_match('/"message"\s*:\s*"([^"]+)"/', $message, $match) === 1) {
            return $match[1];
        }

        return $fallback;
    }
}
