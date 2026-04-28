<?php

namespace App\Livewire\ReceptionScans;

use Livewire\Component;
use Livewire\Attributes\On;

class ScanPanel extends Component
{
    public int   $movementId;
    public int   $total = 0;
    public array $scans = [];

    public function mount(int $movementId, $initialScans): void
    {
        $this->movementId = $movementId;
        $this->total      = $initialScans->count();
        $this->scans      = $initialScans->map(fn($s) => [
            'consignment_type' => $s->consignment_type,
            'item_code'        => $s->parsed_item_code,
            'item_description' => $s->matchedDocumentLine?->item?->description,
            'serial'           => $s->parsed_serial,
            'qty'              => (int) $s->parsed_quantity,
            'status'           => $s->scan_status,
        ])->toArray();
    }

    #[On('scan-completed')]
    public function addScan(array $payload): void
    {
        $statusMap = [
            'matched'   => 'MATCHED',
            'unmatched' => 'UNMATCHED',
            'duplicate' => 'DUPLICATE',
            'error'     => 'ERROR',
        ];

        array_unshift($this->scans, [
            'consignment_type' => $payload['consignment_type'] ?? null,
            'item_code'        => $payload['item_code'] ?? null,
            'item_description' => $payload['item_description'] ?? null,
            'serial'           => $payload['serial'] ?? null,
            'qty'              => (int) ($payload['qty'] ?? 0),
            'status'           => $statusMap[$payload['status']] ?? 'ERROR',
        ]);

        $this->total++;
    }

    public function render()
    {
        return view('livewire.reception-scans.scan-panel');
    }
}
