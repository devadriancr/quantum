<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SyncCatalogsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:catalogs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ejecuta todas las sincronizaciones de catálogos en orden, una tras otra';

    /**
     * Comandos a ejecutar, en el orden exacto en que deben correr.
     *
     * @var array<int, array{command: string, params: array, info: string}>
     */
    protected $steps = [
        [
            'command' => 'sync:projects',
            'params'  => [],
            'info'    => 'Sincronizando proyectos...',
        ],
        [
            'command' => 'sync:item-classes',
            'params'  => [],
            'info'    => 'Sincronizando clases de artículos...',
        ],
        [
            'command' => 'sync:warehouses',
            'params'  => [],
            'info'    => 'Sincronizando almacenes...',
        ],
        [
            'command' => 'sync:locations',
            'params'  => [],
            'info'    => 'Sincronizando ubicaciones...',
        ],
        [
            'command' => 'sync:items',
            'params'  => [],
            'info'    => 'Sincronizando artículos...',
        ],
        [
            'command' => 'sync:transaction-type',
            'params'  => [],
            'info'    => 'Sincronizando tipos de transacción...',
        ],
        [
            'command' => 'sync:stock-limits',
            'params'  => [],
            'info'    => 'Sincronizando límites de stock...',
        ],
        [
            'command' => 'sync:vendor-item-costs',
            'params'  => [],
            'info'    => 'Sincronizando costos de artículos por proveedor...',
        ],
    ];

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        Log::alert("Iniciando sincronización de catálogos");
        $this->info('🚀 Iniciando sincronización de catálogos...');
        $this->newLine();

        $total  = count($this->steps);
        $failed = [];

        foreach ($this->steps as $index => $step) {
            $step['info'] ??= '';
            $this->info('👉 [' . ($index + 1) . "/{$total}] {$step['info']}");

            try {
                $exitCode = $this->call($step['command'], $step['params'] ?? []);

                if ($exitCode !== 0) {
                    $failed[] = $step['command'];
                    $this->error("   ✖ {$step['command']} terminó con código {$exitCode}.");
                }
            } catch (\Throwable $e) {
                $failed[] = $step['command'];
                $this->error("   ✖ {$step['command']} lanzó una excepción: {$e->getMessage()}");
            }

            $this->newLine();
        }

        if (! empty($failed)) {
            $this->error('⚠️  Finalizó con errores en: ' . implode(', ', $failed));

            return self::FAILURE;
        }

        $this->info('🎉 ¡Todas las sincronizaciones se completaron correctamente!');

        return self::SUCCESS;
    }
}
