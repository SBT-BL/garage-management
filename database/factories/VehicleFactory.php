<?php

namespace Database\Factories;

use App\Enums\VehicleType;
use App\Models\Customer;
use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Vehicle>
 */
class VehicleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'customer_id' => Customer::factory(),
            'vehicle_model' => fake()->randomElement(['Honda City', 'Maruti Swift', 'Hyundai i20', 'Royal Enfield Classic', 'Activa 6G']),
            'vehicle_number' => strtoupper(fake()->unique()->bothify('??##??####')),
            'vehicle_type' => fake()->randomElement(VehicleType::cases()),
            'notes' => fake()->optional()->sentence(),
        ];
    }
}
