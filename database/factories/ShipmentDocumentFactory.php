<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ShipmentDocument>
 */
class ShipmentDocumentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $container = \App\Models\Container::inRandomOrder()->first() ?? \App\Models\Container::factory()->create();

        return [
            'container_id' => $container->id,
            'document_number' => 'DOC-' . fake()->unique()->numerify('##########'),
            'partner_id' => $container->partner_id,
            'document_date' => now()->format('Y-m-d'),
            'document_time' => now()->format('H:i:s'),
            'estimated_arrival_date' => $container->estimated_arrival_date,
            'estimated_arrival_time' => $container->estimated_arrival_time,
            'document_status' => 'PENDING',
        ];
    }
}
