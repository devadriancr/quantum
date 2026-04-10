<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class InitDevCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dev:init';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Limpia la base de datos, corre seeds y sincroniza catálogos para el entorno local';

    protected $steps = [
        [
            'command' => 'migrate:fresh',
            'params'  => ['--seed' => true],
            'info'    => 'Recreando la base de datos y corriendo seeds generales...'
        ],
        [
            'command' => 'db:seed',
            'params'  => ['--class' => 'PartnerSeeder'],
            'info'    => 'Sincronizando socios...'
        ],
        [
            'command' => 'sync:projects',
            'params'  => [],
            'info'    => 'Corriendo proyectos...'
        ],
        [
            'command' => 'db:seed',
            'params'  => ['--class' => 'ProjectPrefixSeeder'],
            'info'    => 'Corriendo ProjectPrefixSeeder...'
        ],
        [
            'command' => 'db:seed',
            'params'  => ['--class' => 'MeasurementUnitSeeder'],
            'info'    => 'Corriendo MeasurementUnitSeeder...'
        ],
        [
            'command' => 'db:seed',
            'params'  => ['--class' => 'ItemTypeSeeder'],
            'info'    => 'Sincronizando tipos de artículo...'
        ],
        [
            'command' => 'sync:item-classes',
            'params'  => [],
            'info'    => 'Sincronizando clases de artículos...'
        ],
        [
            'command' => 'sync:warehouses',
            'params'  => [],
            'info'    => 'Sincronizando almacenes...'
        ],
        [
            'command' => 'sync:locations',
            'params'  => [],
            'info'    => 'Sincronizando ubicaciones...'
        ],
        [
            'command' => 'sync:items',
            'params'  => [],
            'info'    => 'Sincronizando artículos...'
        ],
        [
            'command' => 'sync:transaction-types',
            'params'  => [],
            'info'    => 'Sincronizando tipo de transacciones...'
        ]
    ];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // 🛡️ Seguridad
        if (!app()->environment('local')) {
            $this->error('🛑 ¡Alto ahí! Este comando solo se puede ejecutar en el entorno de desarrollo (local).');
            return 1;
        }

        $this->info('🚀 Iniciando la configuración del entorno local...');
        $this->newLine();

        // 🔄 Recorremos dinámicamente todos los pasos
        foreach ($this->steps as $index => $step) {
            $numeroPaso = $index + 1;
            $totalPasos = count($this->steps);

            $this->info("👉 [{$numeroPaso}/{$totalPasos}] {$step['info']}");

            // Ejecutamos el comando de Artisan con sus parámetros
            $this->call($step['command'], $step['params'] ?? []);

            $this->newLine();
        }

        $this->info('🎉 ¡Perfecto! El entorno local ha sido actualizado y está listo para usarse.');

        return 0;
    }
}
