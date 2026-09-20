<?php

namespace App\Services;

use App\Mail\WebsiteLeadMail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class WebsiteLeadMailer
{
    /**
     * @param  array<string, mixed>  $payload
     */
    public function send(array $payload): void
    {
        $recipients = $this->recipients();
        if ($recipients === []) {
            return;
        }
        try {
            Mail::to($recipients)->send(new WebsiteLeadMail($payload));
        } catch (\Throwable $exception) {
            Log::warning('website_lead_mail_failed', ['error' => $exception->getMessage()]);
        }
    }

    /**
     * @return list<string>
     */
    public function recipients(): array
    {
        $raw = (string) config('karnacab.leads_notify_email', '');
        $emails = array_values(array_filter(array_map('trim', explode(',', $raw)), fn ($email) => filter_var($email, FILTER_VALIDATE_EMAIL)));

        return $emails;
    }
}
