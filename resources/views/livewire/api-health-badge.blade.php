<section class="card health" wire:poll.15s="refreshStatus" @if ($status === 'down') data-down="1" @endif>
    <div>
        <strong><span class="health-dot" aria-hidden="true"></span>API health: {{ $status }}</strong>
        <p class="muted" style="margin: 6px 0 0;">{{ $message }}</p>
    </div>
    <span class="muted">Refreshes every 15s</span>
</section>
