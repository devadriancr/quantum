<?php

namespace App\Jobs;

use App\Models\Partner;
use App\Models\Project;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class StoreProjectJob implements ShouldQueue
{
    use Queueable;

    private $code;
    private $model;
    private $partner;

    /**
     * Create a new job instance.
     */
    public function __construct($code, $model, $partner)
    {
        $this->code = $code;
        $this->model = $model;
        $this->partner = $partner;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $partner = Partner::query()->where('code', $this->partner)->first();

        if ($partner !== null) {
            Project::updateOrCreate([
                'code' => $this->code,
            ], [
                'model' => $this->model,
                'partner_id' => $partner->id,
            ]);
        }
    }
}
