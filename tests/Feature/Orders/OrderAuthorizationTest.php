<?php

namespace Tests\Feature\Orders;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_view_orders(): void
    {
        $response = $this->get(route('orders.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_admin_can_view_all_orders(): void
    {
        $admin = User::factory()->admin()->create();
        $client1 = User::factory()->client()->create();
        $client2 = User::factory()->client()->create();

        Order::factory()->for($client1)->create();
        Order::factory()->for($client2)->create();

        $this->actingAs($admin);
        $response = $this->get(route('orders.index'));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('orders/index')
            ->has('orders.data', 2)
        );
    }

    public function test_manager_can_view_all_orders(): void
    {
        $manager = User::factory()->manager()->create();
        $client1 = User::factory()->client()->create();
        $client2 = User::factory()->client()->create();

        Order::factory()->for($client1)->create();
        Order::factory()->for($client2)->create();

        $this->actingAs($manager);
        $response = $this->get(route('orders.index'));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('orders/index')
            ->has('orders.data', 2)
        );
    }

    public function test_client_can_only_view_own_orders(): void
    {
        $client1 = User::factory()->client()->create();
        $client2 = User::factory()->client()->create();

        Order::factory()->for($client1)->create();
        Order::factory()->for($client2)->create();

        $this->actingAs($client1);
        $response = $this->get(route('orders.index'));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('orders/index')
            ->has('orders.data', 1)
        );
    }

    public function test_client_cannot_view_other_users_order(): void
    {
        $client1 = User::factory()->client()->create();
        $client2 = User::factory()->client()->create();

        $order = Order::factory()->for($client2)->create();

        $this->actingAs($client1);
        $response = $this->get(route('orders.show', $order));

        $response->assertForbidden();
    }

    public function test_admin_can_view_any_order(): void
    {
        $admin = User::factory()->admin()->create();
        $client = User::factory()->client()->create();

        $order = Order::factory()->for($client)->create();

        $this->actingAs($admin);
        $response = $this->get(route('orders.show', $order));

        $response->assertOk();
    }

    public function test_all_authenticated_users_can_create_order(): void
    {
        $users = [
            User::factory()->admin()->create(),
            User::factory()->manager()->create(),
            User::factory()->client()->create(),
        ];

        foreach ($users as $user) {
            $this->actingAs($user);

            $response = $this->post(route('orders.store'), [
                'size' => 'Individual',
                'base' => 'Test Base',
                'ingredients' => [],
            ]);

            $this->assertTrue(Order::query()->where('user_id', $user->id)->exists());
        }
    }

    public function test_created_order_assigned_to_authenticated_user(): void
    {
        $client = User::factory()->client()->create();
        $this->actingAs($client);

        $this->post(route('orders.store'), [
            'size' => 'Individual',
            'base' => 'Test Base',
            'ingredients' => [],
        ]);

        $this->assertTrue(Order::query()->where('user_id', $client->id)->exists());
    }

    public function test_admin_can_update_non_completed_order(): void
    {
        $admin = User::factory()->admin()->create();
        $client = User::factory()->client()->create();
        $order = Order::factory()->for($client)->state(['state' => Order::STATE_PENDING])->create();

        $this->actingAs($admin);
        $response = $this->patch(route('orders.update', $order), [
            'state' => Order::STATE_CANCELLED,
        ]);

        $response->assertRedirect(route('orders.index'));
        $this->assertEquals(Order::STATE_CANCELLED, $order->fresh()->state);
    }

    public function test_manager_can_update_non_completed_order(): void
    {
        $manager = User::factory()->manager()->create();
        $client = User::factory()->client()->create();
        $order = Order::factory()->for($client)->state(['state' => Order::STATE_PENDING])->create();

        $this->actingAs($manager);
        $response = $this->patch(route('orders.update', $order), [
            'state' => Order::STATE_CANCELLED,
        ]);

        $response->assertRedirect(route('orders.index'));
        $this->assertEquals(Order::STATE_CANCELLED, $order->fresh()->state);
    }

    public function test_client_can_update_own_pending_order(): void
    {
        $client = User::factory()->client()->create();
        $order = Order::factory()->for($client)->state(['state' => Order::STATE_PENDING])->create();

        $this->actingAs($client);
        $response = $this->patch(route('orders.update', $order), [
            'state' => Order::STATE_CANCELLED,
        ]);

        $response->assertRedirect(route('orders.index'));
        $this->assertEquals(Order::STATE_CANCELLED, $order->fresh()->state);
    }

    public function test_client_cannot_update_other_users_order(): void
    {
        $client1 = User::factory()->client()->create();
        $client2 = User::factory()->client()->create();
        $order = Order::factory()->for($client2)->state(['state' => Order::STATE_PENDING])->create();

        $this->actingAs($client1);
        $response = $this->patch(route('orders.update', $order), [
            'state' => Order::STATE_CANCELLED,
        ]);

        $response->assertForbidden();
        $this->assertEquals(Order::STATE_PENDING, $order->fresh()->state);
    }

    public function test_client_cannot_change_order_to_non_cancelled_state(): void
    {
        $client = User::factory()->client()->create();
        $order = Order::factory()->for($client)->state(['state' => Order::STATE_PENDING])->create();

        $this->actingAs($client);
        $response = $this->patch(route('orders.update', $order), [
            'state' => Order::STATE_COMPLETE,
        ]);

        $response->assertForbidden();
        $this->assertEquals(Order::STATE_PENDING, $order->fresh()->state);
    }

    public function test_no_one_can_update_completed_order(): void
    {
        $users = [
            User::factory()->admin()->create(),
            User::factory()->manager()->create(),
            User::factory()->client()->create(),
        ];

        $client = User::factory()->client()->create();
        $order = Order::factory()->for($client)->state(['state' => Order::STATE_COMPLETE])->create();

        foreach ($users as $user) {
            $this->actingAs($user);
            $response = $this->patch(route('orders.update', $order), [
                'state' => Order::STATE_CANCELLED,
            ]);

            $response->assertForbidden();
            $this->assertEquals(Order::STATE_COMPLETE, $order->fresh()->state);
        }
    }
}
