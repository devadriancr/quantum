<?php

namespace App\Jobs;

use App\Models\Item;
use App\Models\ItemClass;
use App\Models\MeasurementUnit;
use App\Models\PackingSpecification;
use App\Models\Project;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class StoreItemJob implements ShouldQueue
{
    use Queueable;

    protected $partNumber;
    protected $partName;
    protected $measurementUnit;
    protected $itemClass;
    protected $project;
    protected $isObsolete;
    protected $standardPack;
    protected $quantityStandardPack;

    /**
     * Create a new job instance.
     */
    public function __construct($partNumber, $partName, $measurementUnit, $itemClass, $project, $isObsolete, $standardPack, $quantityStandardPack)
    {
        $this->partNumber =  $partNumber;
        $this->partName =  $partName;
        $this->measurementUnit =  $measurementUnit;
        $this->itemClass =  $itemClass;
        $this->project =  $project;
        $this->isObsolete = $isObsolete;
        $this->standardPack =  $standardPack;
        $this->quantityStandardPack = $quantityStandardPack;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $itemClass = ItemClass::query()->where('code', $this->itemClass)->first();
        $packingSpec = PackingSpecification::firstOrCreate(['name' => $this->standardPack, 'quantity' => (int) $this->quantityStandardPack]);
        $measurementUnit = MeasurementUnit::query()->where('code', $this->measurementUnit)->first();
        $item = Item::query()->where([['code', $this->partNumber], ['description', $this->partName]])->first();

        if ($item !== null) {
            $item->update([
                'code' => $this->partNumber,
                'description' => $this->partName,
                'item_class_id' => $itemClass->id,
                'measurement_unit_id' => $measurementUnit ? $measurementUnit->id : null,
                'packing_specification_id' => $packingSpec ? $packingSpec->id : null,
                'active' => ($this->isObsolete == "OBSOLETE") ? false : true,
            ]);
        } else {
            $item = Item::create([
                'code' => $this->partNumber,
                'description' => $this->partName,
                'item_class_id' => $itemClass->id,
                'measurement_unit_id' => $measurementUnit ? $measurementUnit->id : null,
                'packing_specification_id' => $packingSpec ? $packingSpec->id : null,
                'active' => ($this->isObsolete == "OBSOLETE") ? false : true,
            ]);
        }

        switch ($this->project) {
            case '1':
            case '10':
            case '12':
            case '123':
            case '13':
            case '2':
            case '20':
            case '23':
            case '3':
            case '3Y':
            case '4':
            case '45':
            case '47':
            case '5':
            case '56':
            case '57':
            case '6':
            case '7':
            case '710':
            case '79':
            case '8':
            case '811':
            case '9':
                $project = Project::where('code', $this->project)->pluck('id')->toArray();
                $item->projects()->sync($project);
                break;
            default:
                break;
        }
    }
}
