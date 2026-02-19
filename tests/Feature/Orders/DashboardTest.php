<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_the_login_page(): void
    {
        $response = $this->get(route('dashboard'));
        $response->assertRedirect(route('login'));
    }

    public function test_all_authenticated_users_can_visit_dashboard(): void
    {
        $users = [
            User::factory()->admin()->create(),
            User::factory()->manager()->create(),
            User::factory()->client()->create(),
        ];

        foreach ($users as $user) {
            $this->actingAs($user);

            $response = $this->get(route('dashboard'));
            $response->assertOk();
        }
    }

    public function test_dashboard_returns_ingredients(): void
    {
        $user = User::factory()->admin()->create();
        $this->actingAs($user);

        $response = $this->get(route('dashboard'));
        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('Dashboard')
            ->has('ingredients')
        );
    }
}
