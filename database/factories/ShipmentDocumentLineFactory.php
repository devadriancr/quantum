<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ShipmentDocumentLine>
 */
class ShipmentDocumentLineFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'shipment_document_id' => \App\Models\ShipmentDocument::inRandomOrder()->first()->id,
            'serial_number' => fake()->unique()->regexify('SN-[A-Z0-9]{12}'),
            'item_id' => \App\Models\Item::inRandomOrder()->first()->id,
            'quantity_received' => fake()->numberBetween(1, 1000),
            'status' => 'PENDING',
        ];
    }
}
