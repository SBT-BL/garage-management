<?php

namespace Tests\Feature;

use App\Enums\JobCardStatus;
use App\Models\Customer;
use App\Models\JobCard;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerShowTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_view_a_customer(): void
    {
        $customer = Customer::factory()->create();

        $this->get(route('admin.customers.show', $customer))
            ->assertRedirect(route('login'));
    }

    public function test_customer_page_lists_that_customers_job_card_history(): void
    {
        $user = User::factory()->create();
        $customer = Customer::factory()->create();
        $vehicle = Vehicle::factory()->create([
            'customer_id' => $customer->id,
            'vehicle_number' => 'GJ03IN6789',
            'vehicle_model' => 'Splendor',
        ]);
        $jobCard = JobCard::factory()->create([
            'customer_id' => $customer->id,
            'vehicle_id' => $vehicle->id,
            'job_card_number' => 'JC-20261006-0001',
            'date' => '2026-10-06',
            'grand_total' => '875.00',
            'status' => JobCardStatus::Complete,
        ]);
        JobCard::factory()->create([
            'job_card_number' => 'JC-20261006-9999',
            'grand_total' => '9999.00',
        ]);

        $this->actingAs($user)
            ->get(route('admin.customers.show', $customer))
            ->assertOk()
            ->assertSee('Job Card History', false)
            ->assertSee('JC-20261006-0001', false)
            ->assertSee('GJ03IN6789 - Splendor', false)
            ->assertSee('875.00', false)
            ->assertSee('Complete', false)
            ->assertSee(route('admin.job-cards.show', $jobCard), false)
            ->assertDontSee('JC-20261006-9999', false)
            ->assertDontSee('9,999.00', false);
    }

    public function test_customer_page_shows_an_empty_job_card_history(): void
    {
        $user = User::factory()->create();
        $customer = Customer::factory()->create();

        $this->actingAs($user)
            ->get(route('admin.customers.show', $customer))
            ->assertOk()
            ->assertSee('No job cards yet', false);
    }
}
