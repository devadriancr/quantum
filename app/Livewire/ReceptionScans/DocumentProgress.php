<?php

namespace App\Livewire\ReceptionScans;

use App\Models\ShipmentDocumentLine;
use Livewire\Component;
use Livewire\Attributes\On;

class DocumentProgress extends Component
{
    public int   $shipmentDocumentId;
    public array $lines = [];

    public function mount(int $shipmentDocumentId, $documentLines): void
    {
        $this->shipmentDocumentId = $shipmentDocumentId;
        $this->lines = $documentLines->map(fn($line) => [
            'id'               => $line->id,
            'item_code'        => $line->item->code ?? '—',
            'item_description' => $line->item->description ?? '',
            'declared'         => (float) $line->quantity_declared,
            'received'         => (float) ($line->quantity_received_computed ?? 0),
            'status'           => $line->status,
        ])->toArray();
    }

    #[On('scan-completed')]
    public function updateLine(array $payload): void
    {
        if (empty($payload['doc_line_id'])) return;

        foreach ($this->lines as &$line) {
            if ($line['id'] === (int) $payload['doc_line_id']) {
                $line['received'] = (float) ($payload['total_received'] ?? $line['received']);
                if ($line['received'] >= $line['declared'] && $line['declared'] > 0) {
                    $line['status'] = 'RECEIVED';
                }
                break;
            }
        }
        unset($line);
    }

    public function render()
    {
        return view('livewire.reception-scans.document-progress');
    }
}
