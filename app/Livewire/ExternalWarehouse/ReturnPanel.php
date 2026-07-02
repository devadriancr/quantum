<?php

namespace App\Livewire\ExternalWarehouse;

use App\Models\StockMovementLine;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class ReturnPanel extends Component
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

    #[On('scan-return-completed')]
    public function onScanAdded(): void
    {
        $this->resetPage();
    }

    #[On('return-movement-completed')]
    public function onCompleted(): void
    {
        $this->isCompleted = true;
    }

    public function render()
    {
        $scans = StockMovementLine::with('item')
            ->where('stock_movement_id', $this->movementId)
            ->orderByDesc('id')
            ->paginate(10);

        $total = StockMovementLine::where('stock_movement_id', $this->movementId)->count();

        return view('livewire.external-warehouse.return-panel', compact('scans', 'total'));
    }
}
