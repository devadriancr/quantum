<?php

namespace App\Livewire\ReceptionScans;

use Livewire\Component;
use Livewire\Attributes\On;

class Stats extends Component
{
    public int $movementId;
    public int $matched   = 0;
    public int $unmatched = 0;
    public int $duplicate = 0;
    public int $error     = 0;
    public int $total     = 0;

    public function mount(int $movementId, array $stats): void
    {
        $this->movementId = $movementId;
        $this->matched    = $stats['matched'];
        $this->unmatched  = $stats['unmatched'];
        $this->duplicate  = $stats['duplicate'];
        $this->error      = $stats['error'];
        $this->total      = $stats['total'];
    }

    #[On('scan-completed')]
    public function increment(array $payload): void
    {
        match ($payload['status'] ?? '') {
            'matched'   => $this->matched++,
            'unmatched' => $this->unmatched++,
            'duplicate' => $this->duplicate++,
            'error'     => $this->error++,
            default     => null,
        };
        $this->total++;
    }

    public function render()
    {
        return view('livewire.reception-scans.stats');
    }
}
