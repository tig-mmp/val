<?php

namespace App\Http\Controllers;

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

        if (Auth::user()->isClient()) {
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
            $query->where(function (Builder $q) use ($search): void {
                $q->whereAny([
                    'size',
                    'base',
                    'state',
                ], 'like', '%'.$search.'%')
                    ->orWhereHas('user', function (Builder $userQ) use ($search): void {
                        $userQ->where('name', 'like', '%'.$search.'%');
                    })
                    ->orWhereHas('orderIngredients.ingredient', function (Builder $ingredientQ) use ($search): void {
                        $ingredientQ->where('name', 'like', '%'.$search.'%');
                    });
            });
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

        if (Auth::user()->isClient() && $request->state !== Order::STATE_CANCELLED) {
            abort(403, 'Clients can only change orders to cancelled status');
        }

        $order->update([
            'state' => $request->state,
        ]);

        return to_route('orders.index');
    }
}
