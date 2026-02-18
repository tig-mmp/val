<?php

namespace App\Http\Controllers;

use App\Models\Ingredient;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $ingredients = Ingredient::get();

        return Inertia::render('Dashboard', ['ingredients' => $ingredients]);
    }
}
