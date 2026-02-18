<?php

namespace App\Http\Controllers;

use App\Models\Ingredient;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        $ingredients = Ingredient::query()->get();

        return Inertia::render('Dashboard', ['ingredients' => $ingredients]);
    }
}
