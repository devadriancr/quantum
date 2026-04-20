<?php

namespace Database\Seeders;

use App\Models\Container;
use App\Models\ShipmentDocument;
use App\Models\ShipmentDocumentLine;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ContainerDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Creamos 20 contenedores
        Container::factory()->count(20)->create()->each(function ($container) {

            // A cada contenedor, le creamos 1 documento con los mismos datos de llegada
            $document = ShipmentDocument::factory()->create([
                'container_id' => $container->id,
                'partner_id' => $container->partner_id,
                'estimated_arrival_date' => $container->estimated_arrival_date,
                'estimated_arrival_time' => $container->estimated_arrival_time,
            ]);

            // Al documento, le creamos 100 líneas
            // Usamos 'for' para asegurar la cantidad exacta
            for ($i = 1; $i <= 100; $i++) {
                ShipmentDocumentLine::factory()->create([
                    'shipment_document_id' => $document->id,
                    'line_number' => $i, // Numeración del 1 al 100
                ]);
            }
        });
    }
}
