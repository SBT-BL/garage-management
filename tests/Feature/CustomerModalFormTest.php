<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerModalFormTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_customer_create_form(): void
    {
        $this->get(route('admin.customers.create'))
            ->assertRedirect(route('login'));
    }

    public function test_authenticated_user_receives_create_modal_form_fragment(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('admin.customers.create'))
            ->assertOk()
            ->assertSee('name="name"', false)
            ->assertSee('>Create</button>', false)
            ->assertDontSee('<html', false);
    }

    public function test_authenticated_user_receives_edit_modal_form_fragment(): void
    {
        $user = User::factory()->create();
        $customer = Customer::query()->create([
            'name' => 'Harper Brooks',
            'whatsapp_number' => '+1 234-567-8901',
            'address' => '123 Main St',
        ]);

        $this->actingAs($user)
            ->get(route('admin.customers.edit', $customer))
            ->assertOk()
            ->assertSee('Harper Brooks', false)
            ->assertSee('>Update</button>', false)
            ->assertDontSee('<html', false);
    }

    public function test_customers_index_includes_common_modal_and_create_trigger(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('admin.customers.index'))
            ->assertOk()
            ->assertSee('id="commonModal"', false)
            ->assertSee('data-ajax-popup="true"', false)
            ->assertSee(route('admin.customers.create'), false)
            ->assertSee(asset('js/custom.js'), false);
    }

    public function test_storing_customer_redirects_back_to_index(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('admin.customers.store'), [
                'name' => 'Nova Motors',
                'whatsapp_number' => '+1 555-0100',
                'address' => '12 Service Lane',
            ])
            ->assertRedirect(route('admin.customers.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('customers', [
            'name' => 'Nova Motors',
            'whatsapp_number' => '+1 555-0100',
        ]);
    }
}
