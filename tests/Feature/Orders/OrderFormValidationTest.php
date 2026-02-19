<?php

namespace Tests\Feature\Orders;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderFormValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_order_requires_size(): void
    {
        $user = User::factory()->client()->create();
        $this->actingAs($user);

        $response = $this->post(route('orders.store'), [
            'base' => 'Massa Fina',
            'ingredients' => [],
        ]);

        $response->assertSessionHasErrors('size');
    }

    public function test_store_order_requires_base(): void
    {
        $user = User::factory()->client()->create();
        $this->actingAs($user);

        $response = $this->post(route('orders.store'), [
            'size' => 'Individual',
            'ingredients' => [],
        ]);

        $response->assertSessionHasErrors('base');
    }

    public function test_store_order_accepts_valid_data(): void
    {
        $user = User::factory()->client()->create();
        $this->actingAs($user);

        $response = $this->post(route('orders.store'), [
            'size' => 'Individual',
            'base' => 'Massa Fina',
            'ingredients' => [],
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'size' => 'Individual',
            'base' => 'Massa Fina',
        ]);
    }

    public function test_update_order_requires_state(): void
    {
        $user = User::factory()->admin()->create();
        $order = Order::factory()->for($user)->create();
        $this->actingAs($user);

        $response = $this->patch(route('orders.update', $order), []);

        $response->assertSessionHasErrors('state');
    }

    public function test_update_order_with_pending_state_succeeds(): void
    {
        $user = User::factory()->admin()->create();
        $order = Order::factory()->for($user)->state(['state' => Order::STATE_PENDING])->create();
        $this->actingAs($user);

        $response = $this->patch(route('orders.update', $order), [
            'state' => Order::STATE_CANCELLED,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'state' => Order::STATE_CANCELLED,
        ]);
    }

    public function test_update_order_with_completed_state_fails(): void
    {
        $user = User::factory()->admin()->create();
        $order = Order::factory()->for($user)->state(['state' => Order::STATE_COMPLETE])->create();
        $this->actingAs($user);

        $response = $this->patch(route('orders.update', $order), [
            'state' => Order::STATE_CANCELLED,
        ]);

        $response->assertForbidden();
    }
}
