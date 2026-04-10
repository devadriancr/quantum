<?php

namespace App\Jobs;

use App\Models\TransactionType;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class StoreTransactionTypeJob implements ShouldQueue
{
    use Queueable;

    protected $code;
    protected $description;

    /**
     * Create a new job instance.
     */
    public function __construct($code, $description)
    {
        $this->code = $code;
        $this->description = $description;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        TransactionType::updateOrCreate(
            [
                'code' => $this->code,
            ],
            [
                'description' => $this->description,
            ],
        );
    }
}
