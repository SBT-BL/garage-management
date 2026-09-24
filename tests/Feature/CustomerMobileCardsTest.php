<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerMobileCardsTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_customer_cards_feed(): void
    {
        $this->getJson(route('admin.customers.cards'))
            ->assertUnauthorized();
    }

    public function test_authenticated_user_receives_paginated_customer_cards(): void
    {
        $user = User::factory()->create();
        Customer::factory()->count(3)->create();

        $this->actingAs($user)
            ->getJson(route('admin.customers.cards'))
            ->assertOk()
            ->assertJsonPath('meta.total', 3)
            ->assertJsonPath('meta.has_more', false)
            ->assertJsonStructure([
                'html',
                'meta' => ['current_page', 'last_page', 'has_more', 'total'],
            ])
            ->assertSee('mobile-card', false);
    }

    public function test_customer_cards_feed_supports_search_and_pagination(): void
    {
        $user = User::factory()->create();

        Customer::factory()->create(['name' => 'Alpha Garage']);
        Customer::factory()->count(13)->create(['name' => 'Beta Shop']);

        $this->actingAs($user)
            ->getJson(route('admin.customers.cards', ['q' => 'Alpha']))
            ->assertOk()
            ->assertJsonPath('meta.total', 1)
            ->assertSee('Alpha Garage', false);

        $this->actingAs($user)
            ->getJson(route('admin.customers.cards', ['q' => 'Beta', 'page' => 1]))
            ->assertOk()
            ->assertJsonPath('meta.total', 13)
            ->assertJsonPath('meta.has_more', true);

        $this->actingAs($user)
            ->getJson(route('admin.customers.cards', ['q' => 'Beta', 'page' => 2]))
            ->assertOk()
            ->assertJsonPath('meta.has_more', false);
    }

    public function test_customers_index_includes_reusable_mobile_list(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('admin.customers.index'))
            ->assertOk()
            ->assertSee('data-mobile-card-list', false)
            ->assertSee(route('admin.customers.cards'), false)
            ->assertSee('mobile-card-list.js', false)
            ->assertSee('d-none d-lg-block', false);
    }
}
