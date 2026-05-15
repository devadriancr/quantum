<?php

namespace Database\Seeders;

use App\Models\Currency;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;

class CurrencySeeder extends Seeder
{
    // Monedas a sembrar con nombre y símbolo
    private const CURRENCIES = [
        'USD' => ['name' => 'Dólar Americano',      'symbol' => '$'],
        'MXN' => ['name' => 'Peso Mexicano',         'symbol' => '$'],
        'JPY' => ['name' => 'Yen Japonés',           'symbol' => '¥'],
        'CNY' => ['name' => 'Yuan Chino',            'symbol' => '¥'],
        'EUR' => ['name' => 'Euro',                  'symbol' => '€'],
        // 'GBP' => ['name' => 'Libra Esterlina',       'symbol' => '£'],
        // 'CAD' => ['name' => 'Dólar Canadiense',      'symbol' => '$'],
        // 'BRL' => ['name' => 'Real Brasileño',        'symbol' => 'R$'],
        // 'KRW' => ['name' => 'Won Surcoreano',        'symbol' => '₩'],
        // 'AUD' => ['name' => 'Dólar Australiano',     'symbol' => '$'],
        // 'CHF' => ['name' => 'Franco Suizo',          'symbol' => 'Fr'],
        // 'HKD' => ['name' => 'Dólar de Hong Kong',   'symbol' => '$'],
        // 'SGD' => ['name' => 'Dólar de Singapur',    'symbol' => '$'],
        // 'SEK' => ['name' => 'Corona Sueca',          'symbol' => 'kr'],
        // 'NOK' => ['name' => 'Corona Noruega',        'symbol' => 'kr'],
        // 'INR' => ['name' => 'Rupia India',           'symbol' => '₹'],
        // 'TWD' => ['name' => 'Dólar de Taiwán',       'symbol' => '$'],
        // 'THB' => ['name' => 'Baht Tailandés',        'symbol' => '฿'],
        // 'MYR' => ['name' => 'Ringgit Malayo',        'symbol' => 'RM'],
        // 'COP' => ['name' => 'Peso Colombiano',       'symbol' => '$'],
    ];

    public function run(): void
    {
        $rates = $this->fetchRates();

        foreach (self::CURRENCIES as $code => $meta) {
            Currency::updateOrCreate(
                ['code' => $code],
                [
                    'name'         => $meta['name'],
                    'symbol'       => $meta['symbol'],
                    'units_per_usd'=> $rates[$code] ?? 1.0,
                    'active'       => true,
                ]
            );
        }
    }

    private function fetchRates(): array
    {
        try {
            $response = Http::timeout(10)->get('https://api.fxratesapi.com/latest');

            if ($response->successful()) {
                return $response->json('rates', []);
            }
        } catch (\Throwable) {
            // Si la API no responde, usa valores de respaldo aproximados
        }

        $this->command->warn('  ⚠ API de monedas no disponible, usando tasas de respaldo.');

        return [
            'USD' => 1.0,
            'MXN' => 17.50,
            'JPY' => 155.0,
            'CNY' => 7.25,
            'EUR' => 0.92,
            // 'GBP' => 0.79,
            // 'CAD' => 1.36,
            // 'BRL' => 5.10,
            // 'KRW' => 1340.0,
            // 'AUD' => 1.53,
            // 'CHF' => 0.90,
            // 'HKD' => 7.82,
            // 'SGD' => 1.34,
            // 'SEK' => 10.40,
            // 'NOK' => 10.55,
            // 'INR' => 83.50,
            // 'TWD' => 32.10,
            // 'THB' => 36.50,
            // 'MYR' => 4.72,
            // 'COP' => 3950.0,
        ];
    }
}
