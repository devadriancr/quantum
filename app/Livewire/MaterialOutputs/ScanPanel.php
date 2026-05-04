<?php

namespace App\Livewire\MaterialOutputs;

use App\Models\StockMovementLine;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class ScanPanel extends Component
{
    use WithPagination;

    public int    $movementId;
    public bool   $isCompleted;
    public string $locationFrom;
    public string $locationTo;

    public function mount(int $movementId, bool $isCompleted, string $locationFrom, string $locationTo): void
    {
        $this->movementId   = $movementId;
        $this->isCompleted  = $isCompleted;
        $this->locationFrom = $locationFrom;
        $this->locationTo   = $locationTo;
    }

    #[On('scan-output-completed')]
    public function onScanAdded(): void
    {
        $this->resetPage();
    }

    #[On('line-removed')]
    public function onLineRemoved(): void
    {
        $this->resetPage();
    }

    #[On('movement-completed')]
    public function onMovementCompleted(): void
    {
        $this->isCompleted = true;
    }

    public function render()
    {
        $active = fn($q) => $q->whereNull('notes')->orWhere('notes', '!=', 'RETURNED');

        $scans = StockMovementLine::with('item')
            ->where('stock_movement_id', $this->movementId)
            ->where($active)
            ->orderByDesc('id')
            ->paginate(10);

        $total = StockMovementLine::where('stock_movement_id', $this->movementId)
            ->where($active)
            ->count();

        return view('livewire.material-outputs.scan-panel', compact('scans', 'total'));
    }
}
