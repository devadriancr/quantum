<?php

namespace App\Livewire\UnitPlan;

use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Reactive;
use Livewire\Component;

class ContainerPool extends Component
{
    /** Llave del cache donde vive el pool agrupado por fecha. */
    #[Reactive]
    public ?string $cacheKey = null;

    #[Reactive]
    public array $assignments = [];

    public function render()
    {
        $pool = [];
        if ($this->cacheKey) {
            $cached = Cache::get($this->cacheKey, []);
            $pool   = $cached['pool'] ?? [];
        }

        // Construir poolByDate filtrando los asignados — TODO server-side, sólo HTML al cliente
        $poolByDate     = [];
        $totalAvailable = 0;

        foreach ($pool as $date => $bucket) {
            $available = [];
            foreach ($bucket['containers'] as $code => $price) {
                if (isset($this->assignments[$code])) continue;
                $available[$code] = $price;
                $totalAvailable++;
            }
            $poolByDate[$date] = [
                'label'     => $bucket['label'],
                'count'     => count($available),
                'available' => $available,
            ];
        }

        return view('livewire.unit-plan.container-pool', [
            'poolByDate'     => $poolByDate,
            'totalAvailable' => $totalAvailable,
            'hasData'        => !empty($pool),
        ]);
    }
}
