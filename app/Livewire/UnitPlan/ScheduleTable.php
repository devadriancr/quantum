<?php

namespace App\Livewire\UnitPlan;

use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Reactive;
use Livewire\Component;

class ScheduleTable extends Component
{
    /** Llave del cache que apunta al estado pesado server-side. */
    #[Reactive]
    public ?string $cacheKey = null;

    #[Reactive]
    public array $assignments = [];

    #[Reactive]
    public array $slotTimes = [1 => '', 2 => '', 3 => '', 4 => ''];

    #[Reactive]
    public array $days = [];

    public function hasSlotTimeConflict(int $slot): bool
    {
        $t = $this->slotTimes[$slot] ?? '';
        if ($t === '') return false;
        foreach ($this->slotTimes as $s => $time) {
            if ($s !== $slot && $time === $t) return true;
        }
        return false;
    }

    public function render()
    {
        $priceByCode = [];
        if ($this->cacheKey) {
            $cached      = Cache::get($this->cacheKey, []);
            $priceByCode = $cached['price_by_code'] ?? [];
        }

        // Schedule [day][slot] => ['code', 'price'] o null
        $schedule = [];
        for ($d = 1; $d <= 6; $d++) {
            for ($s = 1; $s <= 4; $s++) {
                $schedule[$d][$s] = null;
            }
        }
        $dayTotals = array_fill(1, 6, 0.0);

        foreach ($this->assignments as $code => $pos) {
            $price = $priceByCode[$code] ?? null;
            if ($price === null) continue;
            $schedule[$pos['day']][$pos['slot']] = ['code' => $code, 'price' => $price];
            $dayTotals[$pos['day']] += $price;
        }

        return view('livewire.unit-plan.schedule-table', [
            'schedule'  => $schedule,
            'dayTotals' => $dayTotals,
        ]);
    }
}
