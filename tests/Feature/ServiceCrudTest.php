<?php

namespace Tests\Feature;

use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServiceCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_service_pages(): void
    {
        $service = Service::factory()->create();

        $this->get(route('admin.services.index'))
            ->assertRedirect(route('login'));

        $this->get(route('admin.services.create'))
            ->assertRedirect(route('login'));

        $this->get(route('admin.services.show', $service))
            ->assertRedirect(route('login'));

        $this->getJson(route('admin.services.cards'))
            ->assertUnauthorized();
    }

    public function test_services_index_includes_listing_modal_and_mobile_feed(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('admin.services.index'))
            ->assertOk()
            ->assertSee('Add Service', false)
            ->assertSee('Manage your garage services', false)
            ->assertSee('id="commonModal"', false)
            ->assertSee('data-ajax-popup="true"', false)
            ->assertSee(route('admin.services.create'), false)
            ->assertSee('data-mobile-card-list', false)
            ->assertSee(route('admin.services.cards'), false)
            ->assertSee('id="deleteServiceModal"', false)
            ->assertSee('name="_method" value="DELETE"', false)
            ->assertDontSee('Services coming soon', false);
    }

    public function test_authenticated_user_receives_create_modal_form_fragment(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('admin.services.create'))
            ->assertOk()
            ->assertSee('Service Name', false)
            ->assertSee('name="name"', false)
            ->assertSee('>Create</button>', false)
            ->assertDontSee('<html', false);
    }

    public function test_authenticated_user_receives_edit_modal_form_fragment(): void
    {
        $user = User::factory()->create();
        $service = Service::factory()->create(['name' => 'Oil Change']);

        $this->actingAs($user)
            ->get(route('admin.services.edit', $service))
            ->assertOk()
            ->assertSee('Oil Change', false)
            ->assertSee('>Update</button>', false)
            ->assertDontSee('<html', false);
    }

    public function test_storing_service_redirects_back_to_index(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('admin.services.store'), [
                'name' => '  Bike Wash  ',
            ])
            ->assertRedirect(route('admin.services.index'))
            ->assertSessionHas('success', 'Service created successfully.');

        $this->assertDatabaseHas('services', [
            'name' => 'Bike Wash',
        ]);
    }

    public function test_store_rejects_missing_name(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->from(route('admin.services.index'))
            ->post(route('admin.services.store'), [
                'name' => '   ',
            ])
            ->assertRedirect(route('admin.services.index'))
            ->assertSessionHasErrors('name');

        $this->assertDatabaseCount('services', 0);
    }

    public function test_store_rejects_name_longer_than_255_characters(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->from(route('admin.services.index'))
            ->post(route('admin.services.store'), [
                'name' => str_repeat('A', 256),
            ])
            ->assertRedirect(route('admin.services.index'))
            ->assertSessionHasErrors('name');
    }

    public function test_store_rejects_duplicate_service_name(): void
    {
        $user = User::factory()->create();
        Service::factory()->create(['name' => 'Car Wash']);

        $this->actingAs($user)
            ->from(route('admin.services.index'))
            ->post(route('admin.services.store'), [
                'name' => 'Car Wash',
            ])
            ->assertRedirect(route('admin.services.index'))
            ->assertSessionHasErrors([
                'name' => 'This service name already exists.',
            ]);

        $this->assertDatabaseCount('services', 1);
    }

    public function test_show_displays_service_name(): void
    {
        $user = User::factory()->create();
        $service = Service::factory()->create(['name' => 'Bike Wash']);

        $this->actingAs($user)
            ->get(route('admin.services.show', $service))
            ->assertOk()
            ->assertSee('Bike Wash', false)
            ->assertSee(route('admin.services.edit', $service), false)
            ->assertSee(route('admin.services.destroy', $service), false);
    }

    public function test_update_keeps_the_same_name_and_saves_a_new_name(): void
    {
        $user = User::factory()->create();
        $service = Service::factory()->create(['name' => 'Oil Change']);

        $this->actingAs($user)
            ->put(route('admin.services.update', $service), [
                'name' => 'Oil Change',
            ])
            ->assertRedirect(route('admin.services.show', $service))
            ->assertSessionHas('success', 'Service updated successfully.');

        $this->actingAs($user)
            ->put(route('admin.services.update', $service), [
                'name' => 'Engine Oil Change',
            ])
            ->assertRedirect(route('admin.services.show', $service));

        $this->assertDatabaseHas('services', [
            'id' => $service->id,
            'name' => 'Engine Oil Change',
        ]);
    }

    public function test_update_rejects_another_services_name(): void
    {
        $user = User::factory()->create();
        Service::factory()->create(['name' => 'Car Wash']);
        $service = Service::factory()->create(['name' => 'Bike Wash']);

        $this->actingAs($user)
            ->from(route('admin.services.show', $service))
            ->put(route('admin.services.update', $service), [
                'name' => 'Car Wash',
            ])
            ->assertRedirect(route('admin.services.show', $service))
            ->assertSessionHasErrors([
                'name' => 'This service name already exists.',
            ]);

        $this->assertDatabaseHas('services', [
            'id' => $service->id,
            'name' => 'Bike Wash',
        ]);
    }

    public function test_delete_removes_service_and_redirects_to_index(): void
    {
        $user = User::factory()->create();
        $service = Service::factory()->create(['name' => 'Car Wash']);

        $this->actingAs($user)
            ->delete(route('admin.services.destroy', $service))
            ->assertRedirect(route('admin.services.index'))
            ->assertSessionHas('success', 'Service deleted successfully.');

        $this->assertDatabaseMissing('services', [
            'id' => $service->id,
        ]);
    }

    public function test_service_cards_feed_supports_search_and_pagination(): void
    {
        $user = User::factory()->create();

        Service::factory()->create(['name' => 'Alpha Wash']);
        Service::factory()
            ->count(13)
            ->sequence(fn ($sequence) => [
                'name' => 'Beta Service '.($sequence->index + 1),
            ])
            ->create();

        $this->actingAs($user)
            ->getJson(route('admin.services.cards', ['q' => 'Alpha']))
            ->assertOk()
            ->assertJsonPath('meta.total', 1)
            ->assertSee('Alpha Wash', false);

        $this->actingAs($user)
            ->getJson(route('admin.services.cards', ['q' => 'Beta', 'page' => 1]))
            ->assertOk()
            ->assertJsonPath('meta.total', 13)
            ->assertJsonPath('meta.has_more', true);

        $this->actingAs($user)
            ->getJson(route('admin.services.cards', ['q' => 'Beta', 'page' => 2]))
            ->assertOk()
            ->assertJsonPath('meta.has_more', false);
    }
}
