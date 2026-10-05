<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MobileNavigationTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_view_services_placeholder(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('admin.services.index'))
            ->assertOk()
            ->assertSee('Add Service', false)
            ->assertSee('Services List', false)
            ->assertDontSee('Services coming soon', false)
            ->assertSee('app-bottom-nav', false);
    }

    public function test_authenticated_user_can_view_job_cards_list(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('admin.job-cards.index'))
            ->assertOk()
            ->assertSee('Add Job Card', false)
            ->assertSee('Job Cards List', false)
            ->assertDontSee('Job card form coming soon', false)
            ->assertSee('app-bottom-nav', false);
    }

    public function test_customers_index_includes_mobile_bottom_nav_links(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('admin.customers.index'))
            ->assertOk()
            ->assertSee(route('admin.customers.index'), false)
            ->assertSee(route('admin.services.index'), false)
            ->assertSee(route('admin.job-cards.index'), false)
            ->assertSee('app-bottom-nav-fab', false);
    }

    public function test_guest_cannot_access_services_or_job_cards(): void
    {
        $this->get(route('admin.services.index'))
            ->assertRedirect(route('login'));

        $this->get(route('admin.job-cards.index'))
            ->assertRedirect(route('login'));
    }
}
