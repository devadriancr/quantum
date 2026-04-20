<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Container>
 */
class ContainerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startOfWeek = now()->startOfWeek();
        $endOfWeek = now()->endOfWeek();
        $arrivalDate = fake()->dateTimeBetween($startOfWeek, $endOfWeek)->format('Y-m-d');
        $arrivalHour = fake()->numberBetween(0, 23);

        return [
            'code' => 'CONT-' . fake()->unique()->numerify('#####'),
            'partner_id' => \App\Models\Partner::inRandomOrder()->first()->id,
            'container_type' => fake()->randomElement(['TRUCK', 'CONTAINER', 'BOX', 'PALLET']),
            'estimated_arrival_date' => $arrivalDate,
            'estimated_arrival_time' => sprintf('%02d:00:00', $arrivalHour),
            'status' => 'PENDING',
        ];
    }
}
