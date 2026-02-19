<?php

namespace Tests\Feature;

use App\Models\Ingredient;
use App\Models\Order;
use App\Models\OrderIngredient;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderSearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_search_finds_orders_by_size(): void
    {
        $admin = User::factory()->admin()->create();
        $client = User::factory()->client()->create();

        Order::factory()->for($client)->create(['size' => 'Individual']);
        Order::factory()->for($client)->create(['size' => 'Média']);

        $this->actingAs($admin);
        $response = $this->get(route('orders.index', ['search' => 'Individual']));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->has('orders.data', 1)
        );
    }

    public function test_search_finds_orders_by_base(): void
    {
        $admin = User::factory()->admin()->create();
        $client = User::factory()->client()->create();

        Order::factory()->for($client)->create(['base' => 'Massa Fina']);
        Order::factory()->for($client)->create(['base' => 'Massa Grossa']);

        $this->actingAs($admin);
        $response = $this->get(route('orders.index', ['search' => 'Fina']));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->has('orders.data', 1)
        );
    }

    public function test_search_finds_orders_by_state(): void
    {
        $admin = User::factory()->admin()->create();
        $client = User::factory()->client()->create();

        Order::factory()->for($client)->create(['state' => Order::STATE_PENDING]);
        Order::factory()->for($client)->create(['state' => Order::STATE_COMPLETE]);

        $this->actingAs($admin);
        $response = $this->get(route('orders.index', ['search' => 'Pendente']));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->has('orders.data', 1)
        );
    }

    public function test_search_finds_orders_by_user_name(): void
    {
        $admin = User::factory()->admin()->create();
        $client1 = User::factory()->client()->create(['name' => 'FirstName1 LastName1']);
        $client2 = User::factory()->client()->create(['name' => 'Name2']);

        Order::factory()->for($client1)->create();
        Order::factory()->for($client2)->create();

        $this->actingAs($admin);
        $response = $this->get(route('orders.index', ['search' => 'FirstName1']));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->has('orders.data', 1)
        );
    }

    public function test_search_finds_orders_by_ingredient_name(): void
    {
        $admin = User::factory()->admin()->create();
        $client = User::factory()->client()->create();
        $ingredient = Ingredient::factory()->create(['name' => 'Ingredient']);

        $order = Order::factory()->for($client)->create();
        OrderIngredient::factory()->for($order)->for($ingredient)->create();

        $this->actingAs($admin);
        $response = $this->get(route('orders.index', ['search' => 'Ingredient']));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->has('orders.data', 1)
        );
    }

    public function test_search_is_case_insensitive(): void
    {
        $admin = User::factory()->admin()->create();
        $client = User::factory()->client()->create(['name' => 'FirstName LastName']);

        Order::factory()->for($client)->create();

        $this->actingAs($admin);
        $response = $this->get(route('orders.index', ['search' => 'FirstName']));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->has('orders.data', 1)
        );
    }

    public function test_search_with_empty_string_returns_all(): void
    {
        $admin = User::factory()->admin()->create();
        $client = User::factory()->client()->create();

        Order::factory()->for($client)->create();
        Order::factory()->for($client)->create();

        $this->actingAs($admin);
        $response = $this->get(route('orders.index', ['search' => '']));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->has('orders.data', 2)
        );
    }

    public function test_user_name_filter_works_independently(): void
    {
        $admin = User::factory()->admin()->create();
        $client1 = User::factory()->client()->create(['name' => 'FirstName LastName']);
        $client2 = User::factory()->client()->create(['name' => 'Name2']);

        Order::factory()->for($client1)->create();
        Order::factory()->for($client2)->create();

        $this->actingAs($admin);
        $response = $this->get(route('orders.index', ['user_name' => 'FirstName']));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->has('orders.data', 1)
        );
    }

    public function test_states_filter_works_independently(): void
    {
        $admin = User::factory()->admin()->create();
        $client = User::factory()->client()->create();

        Order::factory()->for($client)->create(['state' => Order::STATE_PENDING]);
        Order::factory()->for($client)->create(['state' => Order::STATE_COMPLETE]);

        $this->actingAs($admin);
        $response = $this->get(route('orders.index', ['states' => [Order::STATE_PENDING]]));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->has('orders.data', 1)
        );
    }

    public function test_all_filters_work_together(): void
    {
        $admin = User::factory()->admin()->create();
        $client1 = User::factory()->client()->create(['name' => 'FirstName1 LastName1']);
        $client2 = User::factory()->client()->create(['name' => 'Name2']);

        Order::factory()->for($client1)->create(['state' => Order::STATE_PENDING, 'size' => 'Individual']);
        Order::factory()->for($client1)->create(['state' => Order::STATE_COMPLETE, 'size' => 'Média']);
        Order::factory()->for($client2)->create(['state' => Order::STATE_PENDING, 'size' => 'Individual']);

        $this->actingAs($admin);
        $response = $this->get(route('orders.index', [
            'search' => 'Individual',
            'user_name' => 'FirstName1',
            'states' => [Order::STATE_PENDING],
        ]));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->has('orders.data', 1)
        );
    }

    public function test_client_search_results_only_show_own_orders(): void
    {
        $client1 = User::factory()->client()->create(['name' => 'Name1']);
        $client2 = User::factory()->client()->create(['name' => 'FirstName2 LastName2']);

        Order::factory()->for($client1)->create();
        Order::factory()->for($client2)->create();

        $this->actingAs($client1);
        $response = $this->get(route('orders.index', ['search' => 'FirstName2']));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->has('orders.data', 0)
        );
    }

    public function test_client_can_search_own_orders(): void
    {
        $client = User::factory()->client()->create();
        $ingredient = Ingredient::factory()->create(['name' => 'Ingredient']);

        $order = Order::factory()->for($client)->create(['size' => 'Individual']);
        OrderIngredient::factory()->for($order)->for($ingredient)->create();

        $this->actingAs($client);
        $response = $this->get(route('orders.index', ['search' => 'Ingredient']));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->has('orders.data', 1)
        );
    }
}
