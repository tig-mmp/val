<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Models\Order;
use App\Models\OrderIngredient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user_name = (string) $request->input('user_name');
        $states = (array) request('states');
        $search = (string) $request->input('search');
        // TODO https://laravel.com/docs/12.x/search

        // optimize query
        // $orders = Order::query()->with(['user', 'ingredients'])->select(['user.name AS userName', 'size','base','state'])->paginate(10);

        $query = Order::query()->with(['user', 'orderIngredients', 'orderIngredients.ingredient']);

        // if (! is_null($user_name) && $user_name !== '') {
        //     $query->where('user.name', 'like', '%' . $user_name . '%');
        // }
        if (! empty($states)) {
            $query->whereIn('state', $states);
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
