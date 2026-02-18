<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Models\Order;
use App\Models\OrderIngredient;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // optimize query
        // $orders = Order::query()->with(['user', 'ingredients'])->select(['user.name AS userName', 'size','base','state'])->paginate(10);
        $orders = Order::with(['user', 'orderIngredients', 'orderIngredients.ingredient'])->paginate(10);

        return Inertia::render('orders/index', [
            'orders' => $orders,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOrderRequest $request)
    {
        $request->validated();

        $order = Order::query()->create([
            'size' => $request->size,
            'base' => $request->base,
        ] + ['user_id' => Auth::user()->id]);
        // TODO optimize
        foreach ($request->ingredients as $ingredient) {
            OrderIngredient::query()->create([
                'order_id' => $order->id,
                'ingredient_id' => $ingredient['id'],
            ]);
        }
        // TODO to_route with success message

        return to_route('dashboard');
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        return Inertia::render('orders/show', [
            'order' => $order,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Order $order)
    {
        return Inertia::render('orders/form', ['order' => $order]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateOrderRequest $request, Order $order)
    {
        $order->update([
            'state' => $request->state,
        ]);

        return to_route('orders.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order)
    {
        if (! $order->orders()->count()) {
            $order->delete();
        } else {
            Order::forceDestroy($order->id);
        }

        return to_route('orders.index');
    }
}
