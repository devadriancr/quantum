<?php

namespace App\Jobs;

use App\Models\ITE;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SyncTransactionTypeJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $transactionTypes = ITE::query()->select('TTYPE AS code', 'TDESC AS description')->orderBy('TTYPE', 'ASC')->get();

        foreach ($transactionTypes as $transactionType) {
            StoreTransactionTypeJob::dispatch(
                $transactionType->code,
                $transactionType->description
            );
        }
    }
}
