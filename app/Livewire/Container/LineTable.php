<?php

namespace App\Livewire\Container;

use App\Models\Item;
use Livewire\Component;
use Livewire\Attributes\Computed;

class LineTable extends Component
{
    public string $search = '';
    public array  $lines  = [];

    public function addItem(int $itemId): void
    {
        foreach ($this->lines as $line) {
            if ($line['item_id'] === $itemId) return;
        }

        $item = Item::find($itemId);
        if (! $item) return;

        $this->lines[] = [
            'item_id'          => $item->id,
            'item_code'        => $item->code,
            'item_description' => $item->description ?? '',
            'quantity'         => 1,
        ];

        $this->search = '';
    }

    public function removeLine(int $index): void
    {
        array_splice($this->lines, $index, 1);
        $this->lines = array_values($this->lines);
    }

    #[Computed]
    public function items()
    {
        if (strlen(trim($this->search)) < 2) {
            return collect();
        }

        $addedIds = array_column($this->lines, 'item_id');

        return Item::whereHas('itemClass', fn($q) => $q->where('code', 'S1'))
            ->where(fn($q) => $q
                ->where('code', 'like', "%{$this->search}%")
                ->orWhere('description', 'like', "%{$this->search}%")
            )
            ->whereNotIn('id', $addedIds)
            ->orderBy('code')
            ->limit(30)
            ->get();
    }

    public function render()
    {
        return view('livewire.container.line-table');
    }
}
