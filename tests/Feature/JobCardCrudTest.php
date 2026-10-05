<?php

namespace Tests\Feature;

use App\Enums\JobCardStatus;
use App\Models\Customer;
use App\Models\JobCard;
use App\Models\JobCardService;
use App\Models\Service;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class JobCardCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_job_card_pages(): void
    {
        $jobCard = JobCard::factory()->create();

        $this->get(route('admin.job-cards.index'))
            ->assertRedirect(route('login'));

        $this->get(route('admin.job-cards.create'))
            ->assertRedirect(route('login'));

        $this->get(route('admin.job-cards.show', $jobCard))
            ->assertRedirect(route('login'));

        $this->get(route('admin.job-cards.edit', $jobCard))
            ->assertRedirect(route('login'));

        $this->post(route('admin.job-cards.store'))
            ->assertRedirect(route('login'));

        $this->put(route('admin.job-cards.update', $jobCard))
            ->assertRedirect(route('login'));

        $this->delete(route('admin.job-cards.destroy', $jobCard))
            ->assertRedirect(route('login'));

        $this->patch(route('admin.job-cards.status.update', $jobCard))
            ->assertRedirect(route('login'));

        $this->getJson(route('admin.job-cards.cards'))
            ->assertUnauthorized();

        $this->getJson(route('admin.select.customers'))
            ->assertUnauthorized();

        $this->getJson(route('admin.select.vehicles'))
            ->assertUnauthorized();

        $this->getJson(route('admin.select.services'))
            ->assertUnauthorized();
    }

    public function test_job_cards_index_includes_listing_and_mobile_feed(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('admin.job-cards.index'))
            ->assertOk()
            ->assertSee('Add Job Card', false)
            ->assertSee('Track repair jobs from intake to delivery', false)
            ->assertSee('Job Cards List', false)
            ->assertSee('id="commonModal"', false)
            ->assertSee(route('admin.job-cards.create'), false)
            ->assertSee('data-mobile-card-list', false)
            ->assertSee(route('admin.job-cards.cards'), false)
            ->assertSee('id="deleteJobCardModal"', false)
            ->assertSee('id="job-card-filter-form"', false)
            ->assertSee('Pending & In Progress')
            ->assertSee('name="customer_id"', false)
            ->assertSee('name="service_id"', false)
            ->assertSee('name="_method" value="DELETE"', false)
            ->assertDontSee('Job card form coming soon', false);
    }

    public function test_create_form_defaults_the_date_and_hides_the_job_card_number(): void
    {
        $user = User::factory()->create();

        $this->travelTo('2026-10-05 09:00:00');

        $this->actingAs($user)
            ->get(route('admin.job-cards.create'))
            ->assertOk()
            ->assertSee('value="2026-10-05"', false)
            ->assertSee('Assigned when you save', false)
            ->assertSee('name="status"', false)
            ->assertSee('Pending', false)
            ->assertSee('Add more service', false)
            ->assertSee('Grand Total', false)
            ->assertSee('name="remark"', false)
            ->assertSee('data-select-target="#customer_id"', false)
            ->assertSee('data-select-target="#vehicle_id"', false)
            ->assertSee('data-select-target="#service-0"', false)
            ->assertSee('data-create-url-template', false)
            ->assertSee('Select a customer first', false)
            ->assertSee(route('admin.select.customers', [], false), false)
            ->assertSee(route('admin.select.vehicles', [], false), false)
            ->assertSee(route('admin.select.services', [], false), false)
            ->assertSee(route('admin.customers.create', [], false), false)
            ->assertSee(route('admin.services.create', [], false), false)
            ->assertDontSee('name="job_card_number"', false)
            ->assertDontSee('name="grand_total"', false);
    }

    public function test_store_creates_job_card_with_generated_number_and_total(): void
    {
        $user = User::factory()->create();
        $customer = Customer::factory()->create();
        $vehicle = Vehicle::factory()->create([
            'customer_id' => $customer->id,
            'vehicle_number' => 'GJ01AB1234',
            'vehicle_model' => 'Swift',
        ]);
        $oilChange = Service::factory()->create(['name' => 'Oil Change']);
        $wash = Service::factory()->create(['name' => 'Bike Wash']);

        $this->travelTo('2026-10-05 09:00:00');

        $this->actingAs($user)
            ->post(route('admin.job-cards.store'), $this->payload($customer, $vehicle, [
                [
                    'service_id' => $oilChange->id,
                    'price' => '250.50',
                    'remark' => 'Synthetic',
                ],
                [
                    'service_id' => $wash->id,
                    'price' => '100',
                    'remark' => '',
                ],
            ], [
                'remark' => '  Collect in the evening  ',
                'status' => JobCardStatus::Pending->value,
            ]))
            ->assertRedirect(route('admin.job-cards.index'))
            ->assertSessionHas('success', 'Job card JC-20261005-0001 created successfully.');

        $jobCard = JobCard::query()->first();

        $this->assertNotNull($jobCard);
        $this->assertSame('JC-20261005-0001', $jobCard->job_card_number);
        $this->assertSame('2026-10-05', $jobCard->date->toDateString());
        $this->assertSame($customer->id, $jobCard->customer_id);
        $this->assertSame($vehicle->id, $jobCard->vehicle_id);
        $this->assertSame('350.50', $jobCard->grand_total);
        $this->assertSame('Collect in the evening', $jobCard->remark);
        $this->assertSame(JobCardStatus::Pending, $jobCard->status);
        $this->assertDatabaseHas('job_card_services', [
            'job_card_id' => $jobCard->id,
            'service_id' => $oilChange->id,
            'price' => '250.50',
            'remark' => 'Synthetic',
        ]);
        $this->assertDatabaseHas('job_card_services', [
            'job_card_id' => $jobCard->id,
            'service_id' => $wash->id,
            'price' => '100.00',
            'remark' => null,
        ]);
    }

    public function test_store_increments_the_job_card_number_for_the_same_day(): void
    {
        $user = User::factory()->create();
        [$customer, $vehicle, $service] = $this->customerVehicleAndService();

        $this->travelTo('2026-10-05 09:00:00');

        $this->actingAs($user)
            ->post(route('admin.job-cards.store'), $this->payload($customer, $vehicle, [
                ['service_id' => $service->id, 'price' => '10', 'remark' => null],
            ]));

        $this->actingAs($user)
            ->post(route('admin.job-cards.store'), $this->payload($customer, $vehicle, [
                ['service_id' => $service->id, 'price' => '20', 'remark' => null],
            ]));

        $numbers = JobCard::query()->orderBy('job_card_number')->pluck('job_card_number')->all();

        $this->assertSame(['JC-20261005-0001', 'JC-20261005-0002'], $numbers);
    }

    public function test_store_rejects_a_missing_service_list(): void
    {
        $user = User::factory()->create();
        [$customer, $vehicle] = $this->customerVehicleAndService();

        $this->actingAs($user)
            ->from(route('admin.job-cards.create'))
            ->post(route('admin.job-cards.store'), $this->payload($customer, $vehicle, []))
            ->assertRedirect(route('admin.job-cards.create'))
            ->assertSessionHasErrors([
                'services' => 'Add at least one service.',
            ]);

        $this->assertDatabaseCount('job_cards', 0);
    }

    public function test_store_rejects_an_invalid_price(): void
    {
        $user = User::factory()->create();
        [$customer, $vehicle, $service] = $this->customerVehicleAndService();

        $this->actingAs($user)
            ->from(route('admin.job-cards.create'))
            ->post(route('admin.job-cards.store'), $this->payload($customer, $vehicle, [
                ['service_id' => $service->id, 'price' => '-5', 'remark' => null],
            ]))
            ->assertRedirect(route('admin.job-cards.create'))
            ->assertSessionHasErrors('services.0.price');

        $this->actingAs($user)
            ->from(route('admin.job-cards.create'))
            ->post(route('admin.job-cards.store'), $this->payload($customer, $vehicle, [
                ['service_id' => $service->id, 'price' => '10.555', 'remark' => null],
            ]))
            ->assertRedirect(route('admin.job-cards.create'))
            ->assertSessionHasErrors('services.0.price');

        $this->assertDatabaseCount('job_cards', 0);
    }

    public function test_store_rejects_a_vehicle_that_belongs_to_another_customer(): void
    {
        $user = User::factory()->create();
        $customer = Customer::factory()->create();
        $otherCustomer = Customer::factory()->create();
        $otherVehicle = Vehicle::factory()->create(['customer_id' => $otherCustomer->id]);
        $service = Service::factory()->create();

        $this->actingAs($user)
            ->from(route('admin.job-cards.create'))
            ->post(route('admin.job-cards.store'), $this->payload($customer, $otherVehicle, [
                ['service_id' => $service->id, 'price' => '80', 'remark' => null],
            ]))
            ->assertRedirect(route('admin.job-cards.create'))
            ->assertSessionHasErrors([
                'vehicle_id' => 'The selected vehicle does not belong to this customer.',
            ]);

        $this->assertDatabaseCount('job_cards', 0);
    }

    public function test_store_rejects_a_submitted_job_card_number_and_grand_total(): void
    {
        $user = User::factory()->create();
        [$customer, $vehicle, $service] = $this->customerVehicleAndService();

        $this->actingAs($user)
            ->from(route('admin.job-cards.create'))
            ->post(route('admin.job-cards.store'), $this->payload($customer, $vehicle, [
                ['service_id' => $service->id, 'price' => '80', 'remark' => null],
            ], [
                'job_card_number' => 'JC-CUSTOM',
                'grand_total' => '1.00',
            ]))
            ->assertRedirect(route('admin.job-cards.create'))
            ->assertSessionHasErrors(['job_card_number', 'grand_total']);

        $this->assertDatabaseCount('job_cards', 0);
    }

    public function test_select2_options_search_customers_vehicles_and_services(): void
    {
        $user = User::factory()->create();
        $customer = Customer::factory()->create(['name' => 'Ravi Patel']);
        Customer::factory()->create(['name' => 'Meet Shah']);
        $owned = Vehicle::factory()->create([
            'customer_id' => $customer->id,
            'vehicle_number' => 'GJ01AB1234',
            'vehicle_model' => 'Swift',
        ]);
        Vehicle::factory()->create([
            'vehicle_number' => 'MH02CD5678',
            'vehicle_model' => 'City',
        ]);
        Service::factory()->create(['name' => 'Oil Change']);
        Service::factory()->create(['name' => 'Bike Wash']);

        $this->actingAs($user)
            ->get(route('admin.select.customers'))
            ->assertForbidden();

        $this->ajaxGet(route('admin.select.customers', ['search' => ['value' => 'Ravi']]))
            ->assertOk()
            ->assertJsonCount(1)
            ->assertJsonPath('0.id', $customer->id)
            ->assertJsonPath('0.text', 'Ravi Patel');

        $this->ajaxGet(route('admin.select.vehicles'))
            ->assertOk()
            ->assertExactJson([]);

        $this->ajaxGet(route('admin.select.vehicles', [
            'customer_id' => $customer->id,
            'search' => ['value' => 'Swift'],
        ]))
            ->assertOk()
            ->assertJsonCount(1)
            ->assertJsonPath('0.id', $owned->id)
            ->assertJsonPath('0.text', 'GJ01AB1234 - Swift');

        $this->ajaxGet(route('admin.select.services', ['search' => ['value' => 'Oil']]))
            ->assertOk()
            ->assertJsonCount(1)
            ->assertJsonPath('0.text', 'Oil Change');
    }

    public function test_json_store_returns_a_selectable_customer_vehicle_and_service(): void
    {
        $user = User::factory()->create();
        $customer = Customer::factory()->create();

        $customerResponse = $this->actingAs($user)
            ->postJson(route('admin.customers.store'), [
                'name' => 'New Buyer',
                'whatsapp_number' => '9876543210',
                'address' => 'Ring road',
            ])
            ->assertOk()
            ->assertJsonPath('text', 'New Buyer');

        $this->assertDatabaseHas('customers', [
            'id' => $customerResponse->json('id'),
            'name' => 'New Buyer',
        ]);

        $this->actingAs($user)
            ->postJson(route('admin.customers.store'), [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['name', 'whatsapp_number']);

        $this->actingAs($user)
            ->postJson(route('admin.customers.vehicles.store', $customer), [
                'vehicle_model' => 'Swift',
                'vehicle_number' => 'GJ01AB9999',
                'vehicle_type' => 'Car',
            ])
            ->assertOk()
            ->assertJsonPath('text', 'GJ01AB9999 - Swift');

        $this->assertDatabaseHas('vehicles', [
            'customer_id' => $customer->id,
            'vehicle_number' => 'GJ01AB9999',
        ]);

        $this->actingAs($user)
            ->postJson(route('admin.services.store'), ['name' => 'Wheel Alignment'])
            ->assertOk()
            ->assertJsonPath('text', 'Wheel Alignment');

        $this->assertDatabaseHas('services', ['name' => 'Wheel Alignment']);
    }

    public function test_edit_form_preselects_customer_vehicle_and_service(): void
    {
        $user = User::factory()->create();
        $customer = Customer::factory()->create(['name' => 'Ravi Patel']);
        $vehicle = Vehicle::factory()->create([
            'customer_id' => $customer->id,
            'vehicle_number' => 'GJ01AB1234',
            'vehicle_model' => 'Swift',
        ]);
        $service = Service::factory()->create(['name' => 'Oil Change']);
        $jobCard = JobCard::factory()->create([
            'customer_id' => $customer->id,
            'vehicle_id' => $vehicle->id,
        ]);
        JobCardService::factory()->create([
            'job_card_id' => $jobCard->id,
            'service_id' => $service->id,
            'price' => '80.00',
        ]);

        $this->actingAs($user)
            ->get(route('admin.job-cards.edit', $jobCard))
            ->assertOk()
            ->assertSee('>Ravi Patel</option>', false)
            ->assertSee('>GJ01AB1234 - Swift</option>', false)
            ->assertSee('>Oil Change</option>', false)
            ->assertSee(route('admin.customers.vehicles.create', $customer, false), false)
            ->assertSee('data-select-target="#service-0"', false);
    }

    public function test_show_displays_job_card_details_and_service_lines(): void
    {
        $user = User::factory()->create();
        $customer = Customer::factory()->create(['name' => 'Ravi Patel']);
        $vehicle = Vehicle::factory()->create([
            'customer_id' => $customer->id,
            'vehicle_number' => 'GJ01AB1234',
            'vehicle_model' => 'Swift',
        ]);
        $service = Service::factory()->create(['name' => 'Oil Change']);
        $jobCard = JobCard::factory()->create([
            'customer_id' => $customer->id,
            'vehicle_id' => $vehicle->id,
            'job_card_number' => 'JC-20261005-0007',
            'remark' => 'Ready by evening',
            'grand_total' => '250.50',
            'status' => JobCardStatus::InProgress,
        ]);
        JobCardService::factory()->create([
            'job_card_id' => $jobCard->id,
            'service_id' => $service->id,
            'price' => '250.50',
            'remark' => 'Synthetic',
        ]);

        $this->actingAs($user)
            ->get(route('admin.job-cards.show', $jobCard))
            ->assertOk()
            ->assertSee('JC-20261005-0007', false)
            ->assertSee('Ravi Patel', false)
            ->assertSee('GJ01AB1234 - Swift', false)
            ->assertSee('In Progress', false)
            ->assertSee('Ready by evening', false)
            ->assertSee('Oil Change', false)
            ->assertSee('Synthetic', false)
            ->assertSee('250.50', false)
            ->assertSee(route('admin.job-cards.edit', $jobCard), false)
            ->assertSee(route('admin.job-cards.destroy', $jobCard), false);
    }

    public function test_update_replaces_service_lines_and_recalculates_the_total(): void
    {
        $user = User::factory()->create();
        $customer = Customer::factory()->create();
        $vehicle = Vehicle::factory()->create(['customer_id' => $customer->id]);
        $original = Service::factory()->create(['name' => 'Oil Change']);
        $replacement = Service::factory()->create(['name' => 'Brake Service']);
        $jobCard = JobCard::factory()->create([
            'customer_id' => $customer->id,
            'vehicle_id' => $vehicle->id,
            'job_card_number' => 'JC-20261005-0003',
            'grand_total' => '100.00',
            'status' => JobCardStatus::Pending,
        ]);
        $line = JobCardService::factory()->create([
            'job_card_id' => $jobCard->id,
            'service_id' => $original->id,
            'price' => '100.00',
        ]);

        $this->actingAs($user)
            ->put(route('admin.job-cards.update', $jobCard), $this->payload($customer, $vehicle, [
                [
                    'service_id' => $replacement->id,
                    'price' => '450',
                    'remark' => 'Front pads',
                ],
            ], [
                'status' => JobCardStatus::Complete->value,
                'remark' => 'Delivered',
            ]))
            ->assertRedirect(route('admin.job-cards.show', $jobCard))
            ->assertSessionHas('success', 'Job card JC-20261005-0003 updated successfully.');

        $jobCard->refresh();

        $this->assertSame('JC-20261005-0003', $jobCard->job_card_number);
        $this->assertSame('450.00', $jobCard->grand_total);
        $this->assertSame(JobCardStatus::Complete, $jobCard->status);
        $this->assertSame('Delivered', $jobCard->remark);
        $this->assertDatabaseMissing('job_card_services', ['id' => $line->id]);
        $this->assertDatabaseHas('job_card_services', [
            'job_card_id' => $jobCard->id,
            'service_id' => $replacement->id,
            'price' => '450.00',
            'remark' => 'Front pads',
        ]);
        $this->assertDatabaseCount('job_card_services', 1);
    }

    public function test_delete_removes_the_job_card_and_its_service_lines(): void
    {
        $user = User::factory()->create();
        $jobCard = JobCard::factory()->create();
        $line = JobCardService::factory()->create(['job_card_id' => $jobCard->id]);

        $this->actingAs($user)
            ->delete(route('admin.job-cards.destroy', $jobCard))
            ->assertRedirect(route('admin.job-cards.index'))
            ->assertSessionHas('success', 'Job card deleted successfully.');

        $this->assertDatabaseMissing('job_cards', ['id' => $jobCard->id]);
        $this->assertDatabaseMissing('job_card_services', ['id' => $line->id]);
    }

    public function test_cards_feed_searches_by_number_customer_and_vehicle(): void
    {
        $user = User::factory()->create();
        $customer = Customer::factory()->create(['name' => 'Ravi Patel']);
        $vehicle = Vehicle::factory()->create([
            'customer_id' => $customer->id,
            'vehicle_number' => 'GJ01AB1234',
            'vehicle_model' => 'Swift',
        ]);
        JobCard::factory()->create([
            'customer_id' => $customer->id,
            'vehicle_id' => $vehicle->id,
            'job_card_number' => 'JC-20261005-0042',
        ]);
        JobCard::factory()->create([
            'job_card_number' => 'JC-20261005-0099',
        ]);

        $this->actingAs($user)
            ->getJson(route('admin.job-cards.cards', ['q' => '0042']))
            ->assertOk()
            ->assertJsonPath('meta.total', 1)
            ->assertSee('Ravi Patel', false)
            ->assertSee('GJ01AB1234 - Swift', false)
            ->assertSee('Pending', false);

        $this->actingAs($user)
            ->getJson(route('admin.job-cards.cards', ['q' => 'Ravi']))
            ->assertOk()
            ->assertJsonPath('meta.total', 1);

        $this->actingAs($user)
            ->getJson(route('admin.job-cards.cards', ['q' => 'GJ01AB1234']))
            ->assertOk()
            ->assertJsonPath('meta.total', 1)
            ->assertSee('GJ01AB1234 - Swift', false);
    }

    public function test_cards_feed_filters_by_status(): void
    {
        $user = User::factory()->create();
        JobCard::factory()->create(['status' => JobCardStatus::Pending]);
        JobCard::factory()->create(['status' => JobCardStatus::Complete]);

        $response = $this->actingAs($user)
            ->getJson(route('admin.job-cards.cards', ['status' => JobCardStatus::Complete->value]));

        $response->assertOk()
            ->assertJsonPath('meta.total', 1);

        $html = $response->json('html');

        $this->assertStringContainsString('value="Complete" selected', $html);
        $this->assertStringNotContainsString('value="Pending" selected', $html);
    }

    public function test_listing_status_can_be_changed_without_editing_the_job_card(): void
    {
        $user = User::factory()->create();
        $jobCard = JobCard::factory()->create([
            'status' => JobCardStatus::Pending,
            'remark' => 'Keep this remark',
            'grand_total' => '125.00',
        ]);

        $this->actingAs($user)
            ->patchJson(route('admin.job-cards.status.update', $jobCard), [
                'status' => JobCardStatus::InProgress->value,
            ])
            ->assertOk()
            ->assertJsonPath('status', 'In Progress')
            ->assertJsonPath('badge_class', 'text-bg-info');

        $jobCard->refresh();

        $this->assertSame(JobCardStatus::InProgress, $jobCard->status);
        $this->assertSame('Keep this remark', $jobCard->remark);
        $this->assertSame('125.00', $jobCard->grand_total);

        $this->actingAs($user)
            ->patchJson(route('admin.job-cards.status.update', $jobCard), [
                'status' => 'Delivered',
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('status');

        $this->assertSame(JobCardStatus::InProgress, $jobCard->refresh()->status);
    }

    public function test_listing_defaults_to_the_last_seven_days_and_open_statuses(): void
    {
        $user = User::factory()->create();

        $this->travelTo('2026-10-05 09:00:00');

        $pending = JobCard::factory()->create([
            'status' => JobCardStatus::Pending,
            'date' => '2026-10-05',
            'job_card_number' => 'JC-20261005-0101',
        ]);
        JobCard::factory()->create([
            'status' => JobCardStatus::Complete,
            'date' => '2026-10-05',
            'job_card_number' => 'JC-20261005-0102',
        ]);
        JobCard::factory()->create([
            'status' => JobCardStatus::Pending,
            'date' => '2026-09-20',
            'job_card_number' => 'JC-20260920-0103',
        ]);

        $this->actingAs($user)
            ->get(route('admin.job-cards.index'))
            ->assertOk()
            ->assertSee('value="2026-09-29"', false)
            ->assertSee('value="2026-10-05"', false);

        $this->actingAs($user)
            ->getJson(route('admin.job-cards.cards'))
            ->assertOk()
            ->assertJsonPath('meta.total', 1)
            ->assertSee($pending->customer->name, false)
            ->assertDontSee('JC-20261005-0102', false)
            ->assertDontSee('JC-20260920-0103', false);
    }

    public function test_listing_filters_by_customer_service_and_status(): void
    {
        $user = User::factory()->create();
        $customer = Customer::factory()->create(['name' => 'Filter Customer']);
        $otherCustomer = Customer::factory()->create(['name' => 'Other Customer']);
        $vehicle = Vehicle::factory()->create(['customer_id' => $customer->id]);
        $otherVehicle = Vehicle::factory()->create(['customer_id' => $otherCustomer->id]);
        $oilChange = Service::factory()->create(['name' => 'Filter Oil']);
        $wash = Service::factory()->create(['name' => 'Filter Wash']);

        $matching = JobCard::factory()->create([
            'customer_id' => $customer->id,
            'vehicle_id' => $vehicle->id,
            'status' => JobCardStatus::Pending,
            'date' => '2026-10-04',
        ]);
        JobCardService::factory()->create([
            'job_card_id' => $matching->id,
            'service_id' => $oilChange->id,
        ]);

        $other = JobCard::factory()->create([
            'customer_id' => $otherCustomer->id,
            'vehicle_id' => $otherVehicle->id,
            'status' => JobCardStatus::InProgress,
            'date' => '2026-10-04',
        ]);
        JobCardService::factory()->create([
            'job_card_id' => $other->id,
            'service_id' => $wash->id,
        ]);

        $this->travelTo('2026-10-05 09:00:00');

        $this->actingAs($user)
            ->getJson(route('admin.job-cards.cards', [
                'start_date' => '2026-10-01',
                'end_date' => '2026-10-05',
                'status' => 'all',
                'customer_id' => $customer->id,
                'service_id' => $oilChange->id,
            ]))
            ->assertOk()
            ->assertJsonPath('meta.total', 1)
            ->assertSee('Filter Customer', false)
            ->assertDontSee('Other Customer', false);
    }

    public function test_vehicle_used_on_a_job_card_cannot_be_deleted(): void
    {
        $user = User::factory()->create();
        $customer = Customer::factory()->create();
        $vehicle = Vehicle::factory()->create(['customer_id' => $customer->id]);
        JobCard::factory()->create([
            'customer_id' => $customer->id,
            'vehicle_id' => $vehicle->id,
        ]);

        $this->actingAs($user)
            ->delete(route('admin.customers.vehicles.destroy', [$customer, $vehicle]))
            ->assertRedirect(route('admin.customers.show', $customer))
            ->assertSessionHas('error', 'This vehicle cannot be deleted because related records still exist.');

        $this->assertDatabaseHas('vehicles', ['id' => $vehicle->id]);
    }

    public function test_service_used_on_a_job_card_cannot_be_deleted(): void
    {
        $user = User::factory()->create();
        $service = Service::factory()->create(['name' => 'Oil Change']);
        $jobCard = JobCard::factory()->create();
        JobCardService::factory()->create([
            'job_card_id' => $jobCard->id,
            'service_id' => $service->id,
        ]);

        $this->actingAs($user)
            ->delete(route('admin.services.destroy', $service))
            ->assertRedirect(route('admin.services.show', $service))
            ->assertSessionHas('error', 'This service cannot be deleted because related records still exist.');

        $this->assertDatabaseHas('services', ['id' => $service->id]);
    }

    private function ajaxGet(string $uri): TestResponse
    {
        return $this->withHeaders([
            'X-Requested-With' => 'XMLHttpRequest',
        ])->getJson($uri);
    }

    /**
     * @param  list<array{service_id: int, price: string, remark: ?string}>  $services
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function payload(Customer $customer, Vehicle $vehicle, array $services, array $overrides = []): array
    {
        return array_merge([
            'date' => '2026-10-05',
            'customer_id' => $customer->id,
            'vehicle_id' => $vehicle->id,
            'status' => JobCardStatus::Pending->value,
            'remark' => null,
            'services' => $services,
        ], $overrides);
    }

    /**
     * @return array{0: Customer, 1: Vehicle, 2: Service}
     */
    private function customerVehicleAndService(): array
    {
        $customer = Customer::factory()->create();
        $vehicle = Vehicle::factory()->create(['customer_id' => $customer->id]);
        $service = Service::factory()->create();

        return [$customer, $vehicle, $service];
    }
}
