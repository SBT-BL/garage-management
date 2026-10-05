<?php

namespace Database\Factories;

use App\Enums\JobCardStatus;
use App\Models\Customer;
use App\Models\JobCard;
use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<JobCard>
 */
class JobCardFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'job_card_number' => 'JC-'.now()->format('Ymd').'-'.str_pad((string) fake()->unique()->numberBetween(1, 9999), 4, '0', STR_PAD_LEFT),
            'date' => now()->toDateString(),
            'customer_id' => Customer::factory(),
            'vehicle_id' => function (array $attributes): int {
                return Vehicle::factory()->create([
                    'customer_id' => $attributes['customer_id'],
                ])->id;
            },
            'grand_total' => fake()->randomFloat(2, 100, 5000),
            'remark' => fake()->optional()->sentence(),
            'status' => JobCardStatus::Pending,
        ];
    }
}
