<?php

namespace App\Jobs;

use App\Models\Currency;
use App\Models\Item;
use App\Models\ItemCost;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class StoreVendorItemCostsJob implements ShouldQueue
{
    use Queueable;

    protected string $itemNumber;
    protected string $vendorNumber;
    protected string $currencyCode;
    protected string $startingDate;
    protected string $endDate;
    protected string $totalCost;

    public function __construct(string $itemNumber, string $vendorNumber, string $currencyCode, string $startingDate, string $endDate, string $totalCost)
    {
        $this->itemNumber   = $itemNumber;
        $this->vendorNumber = $vendorNumber;
        $this->currencyCode = $currencyCode;
        $this->startingDate = $startingDate;
        $this->endDate      = $endDate;
        $this->totalCost    = $totalCost;
    }

    public function handle(): void
    {
        $item     = Item::query()->where('code', $this->itemNumber)->first();
        $currency = Currency::query()->where('code', $this->currencyCode)->first();

        if (!$item || !$currency) {
            return;
        }

        $startDate = $this->parseDate($this->startingDate);

        if (!$startDate) {
            return; // sin fecha de inicio válida, no se puede guardar
        }

        $endDate = $this->parseDate($this->endDate);

        if ($endDate === null) {
            $item->update([
                'last_unit_cost'     => $this->totalCost,
                'last_currency_code' => $currency->code,
            ]);
        }

        ItemCost::query()->updateOrCreate(
            [
                'item_id'       => $item->id,
                'currency_id'   => $currency->id,
                'vendor_number' => $this->vendorNumber,
                'start_date'    => $startDate,
            ],
            [
                'end_date'   => $endDate,
                'total_cost' => $this->totalCost,
            ]
        );
    }

    /**
     * Parsea fechas del sistema legado en formato Ymd (ej: 20260513).
     * Retorna null si el valor es inválido o representa "sin fecha" (0, '', 99999999).
     */
    private function parseDate(string $value): ?string
    {
        $clean = trim($value);

        if (in_array($clean, ['99999999', '0', ''], true)) {
            return null;
        }

        if (!preg_match('/^\d{8}$/', $clean)) {
            return null;
        }

        return Carbon::createFromFormat('Ymd', $clean)->format('Y-m-d');
    }
}
