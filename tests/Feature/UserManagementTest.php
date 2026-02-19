<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_non_admin_cannot_view_users_list(): void
    {
        $users = [
            User::factory()->manager()->create(),
            User::factory()->client()->create(),
        ];

        foreach ($users as $user) {
            $this->actingAs($user);
            $response = $this->get(route('users.index'));
            $response->assertForbidden();
        }
    }

    public function test_admin_can_view_users_list(): void
    {
        $admin = User::factory()->admin()->create();
        User::factory()->create();
        User::factory()->create();

        $this->actingAs($admin);
        $response = $this->get(route('users.index'));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('users/index')
            ->has('users')
        );
    }

    public function test_non_admin_cannot_create_user(): void
    {
        $users = [
            User::factory()->manager()->create(),
            User::factory()->client()->create(),
        ];

        foreach ($users as $user) {
            $this->actingAs($user);
            $response = $this->post(route('users.store'), [
                'name' => 'New User',
                'email' => 'newEmail'.$user->id.'@example.com',
                'password' => 'password123',
                'password_confirmation' => 'password123',
            ]);

            $response->assertForbidden();
            $this->assertFalse(User::query()->where('email', 'newEmail'.$user->id.'@example.com')->exists());
        }
    }

    public function test_admin_can_create_user(): void
    {
        $admin = User::factory()->admin()->create();
        $this->actingAs($admin);

        $response = $this->post(route('users.store'), [
            'name' => 'New User',
            'email' => 'newEmail@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect();
        $this->assertTrue(User::query()->where('email', 'newEmail@example.com')->exists());
    }

    public function test_admin_cannot_create_user_with_duplicate_email(): void
    {
        $admin = User::factory()->admin()->create();
        User::factory()->create(['email' => 'existing@example.com']);

        $this->actingAs($admin);
        $response = $this->post(route('users.store'), [
            'name' => 'New User',
            'email' => 'existing@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_non_admin_cannot_edit_user(): void
    {
        $users = [
            User::factory()->manager()->create(),
            User::factory()->client()->create(),
        ];
        $targetUser = User::factory()->create();

        foreach ($users as $user) {
            $this->actingAs($user);
            $response = $this->patch(route('users.update', $targetUser), [
                'name' => 'Updated Name',
                'email' => 'updated'.$user->id.'@example.com',
            ]);

            $response->assertForbidden();
        }
    }

    public function test_admin_can_edit_user(): void
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->create(['name' => 'Old Name']);

        $this->actingAs($admin);
        $response = $this->patch(route('users.update', $user), [
            'name' => 'Updated Name',
            'email' => $user->email,
        ]);

        $response->assertRedirect();
        $this->assertEquals('Updated Name', $user->fresh()->name);
    }

    public function test_admin_can_delete_user(): void
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->create();

        $this->actingAs($admin);
        $response = $this->delete(route('users.destroy', $user));

        $response->assertRedirect();
        $this->assertSoftDeleted('users', ['id' => $user->id]);
    }

    public function test_non_admin_cannot_delete_user(): void
    {
        $users = [
            User::factory()->manager()->create(),
            User::factory()->client()->create(),
        ];
        $targetUser = User::factory()->create();

        foreach ($users as $user) {
            $this->actingAs($user);
            $response = $this->delete(route('users.destroy', $targetUser));

            $response->assertForbidden();
            $this->assertNotSoftDeleted('users', ['id' => $targetUser->id]);
        }
    }

    public function test_admin_can_view_user_details(): void
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->create();

        $this->actingAs($admin);
        $response = $this->get(route('users.show', $user));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('users/show')
        );
    }

    public function test_non_admin_cannot_view_user_details(): void
    {
        $users = [
            User::factory()->manager()->create(),
            User::factory()->client()->create(),
        ];
        $targetUser = User::factory()->create();

        foreach ($users as $user) {
            $this->actingAs($user);
            $response = $this->get(route('users.show', $targetUser));

            $response->assertForbidden();
        }
    }

    public function test_non_admin_cannot_view_user_edit_form(): void
    {
        $users = [
            User::factory()->manager()->create(),
            User::factory()->client()->create(),
        ];
        $targetUser = User::factory()->create();

        foreach ($users as $user) {
            $this->actingAs($user);
            $response = $this->get(route('users.edit', $targetUser));

            $response->assertForbidden();
        }
    }

    public function test_admin_can_view_user_edit_form(): void
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->create();

        $this->actingAs($admin);
        $response = $this->get(route('users.edit', $user));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('users/form')
        );
    }
}
