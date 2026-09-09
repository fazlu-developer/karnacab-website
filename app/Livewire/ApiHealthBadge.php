<?php

namespace App\Livewire;

use App\Services\LaravelApiClient;
use Livewire\Component;
use Throwable;

class ApiHealthBadge extends Component
{
    public string $status = 'checking';

    public string $message = 'Checking Laravel API...';

    public function mount(LaravelApiClient $api): void
    {
        $this->refreshStatus($api);
    }

    public function refreshStatus(LaravelApiClient $api): void
    {
        try {
            $payload = $api->health();
            $this->status = isset($payload['products']) ? 'ok' : 'unknown';
            $this->message = sprintf(
                'Laravel booking API %s — %s',
                $this->status,
                $api->baseUrl()
            );
        } catch (Throwable $exception) {
            $this->status = 'down';
            $this->message = 'Laravel API unreachable: '.$exception->getMessage();
        }
    }

    public function render()
    {
        return view('livewire.api-health-badge');
    }
}
