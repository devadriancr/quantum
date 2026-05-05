<?php

namespace App\Jobs;

use App\Models\Item;
use App\Models\Location;
use App\Models\StockLimit;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class StoreStockLimitsJob implements ShouldQueue
{
    use Queueable;

    private $partNumber;
    private $stockMin;
    private $stockMax;

    /**
     * Create a new job instance.
     */
    public function __construct(String $partNumber, int $stockMin, int $stockMax)
    {
        $this->partNumber = $partNumber;
        $this->stockMin = $stockMin;
        $this->stockMax = $stockMax;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $location = Location::query()->where('code', 'L60')->firstOrFail();
        $item = Item::query()->where('code', $this->partNumber)->firstOrFail();

        StockLimit::updateOrCreate([
            'item_id' => $item->id,
            'location_id' => $location->id,
        ], [
            'minimum_quantity' => $this->stockMin,
            'maximum_quantity' => $this->stockMax,
        ]);

        Log::info("Stock limits for item {$this->partNumber} at location L60 have been updated: Min={$this->stockMin}, Max={$this->stockMax}");
    }
}
