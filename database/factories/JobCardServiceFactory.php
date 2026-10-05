<?php

namespace Database\Factories;

use App\Models\JobCard;
use App\Models\JobCardService;
use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<JobCardService>
 */
class JobCardServiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'job_card_id' => JobCard::factory(),
            'service_id' => Service::factory(),
            'price' => fake()->randomFloat(2, 50, 2000),
            'remark' => fake()->optional()->sentence(),
        ];
    }
}
