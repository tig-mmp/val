<?php

namespace Database\Seeders;

use App\Models\Ingredient;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // TODO check if there is a better way of doing this for loop
        $ingredientNames = ['Chourição', 'Fiambre', 'Queijo'];
        foreach ($ingredientNames as $ingredientName) {
            Ingredient::factory()->create([
                'name' => $ingredientName,
            ]);

        }

        User::factory(3)->client()->create();
        User::factory(3)->client()->create()->withOrders(3);
        User::factory(5)->manager()->create();
        User::factory(1)->admin()->create();

        User::factory()->admin()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
        ]);
    }
}
