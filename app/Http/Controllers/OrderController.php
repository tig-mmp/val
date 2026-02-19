<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Models\Order;
use App\Models\OrderIngredient;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $this->authorize('viewAny', Order::class);

        $user_name = (string) $request->input('user_name');
        $states = (array) request('states');
        $search = (string) $request->input('search');

        $query = Order::query()->with(['user', 'orderIngredients', 'orderIngredients.ingredient']);

        if (Auth::user()->role === UserRole::Client) {
            $query->where('user_id', Auth::user()->id);
        }

        if ($user_name !== '') {
            $query->whereHas('user', function (Builder $q) use ($user_name): void {
                $q->where('name', 'like', '%'.$user_name.'%');
            });
        }

        if ($states !== []) {
            $query->whereIn('state', $states);
        }

        if ($search !== '') {
            $query->whereAny([
                'size',
                'base',
                'state',
            ], 'like', '%'.$search.'%');
        }

        $orders = $query->paginate(10);

        return Inertia::render('orders/index', [
            'orders' => $orders,
            'user_name' => $user_name,
            'states' => $states,
            'search' => $search,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOrderRequest $request): RedirectResponse
    {
        $this->authorize('create', Order::class);

        $request->validated();

        $order = Order::query()->create([
            'size' => $request->size,
            'base' => $request->base,
        ] + ['user_id' => Auth::user()->id]);

        foreach ($request->ingredients as $ingredient) {
            OrderIngredient::query()->create([
                'order_id' => $order->id,
                'ingredient_id' => $ingredient['id'],
            ]);
        }

        return to_route('dashboard');
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order): Response
    {
        $this->authorize('view', $order);

        return Inertia::render('orders/show', [
            'order' => $order,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Order $order): Response
    {
        $this->authorize('update', $order);

        return Inertia::render('orders/form', ['order' => $order]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateOrderRequest $request, Order $order): RedirectResponse
    {
        $this->authorize('update', $order);

        $request->validated();

        $order->update([
            'state' => $request->state,
        ]);

        return to_route('orders.index');
    }
}
