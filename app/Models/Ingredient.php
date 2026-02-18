<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Ingredient extends Model
{
    /** @use HasFactory<\Database\Factories\IngredientFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
    ];

    public function orders(): BelongsToMany
    {
        return $this->belongsToMany(Order::class)->using(OrderIngredient::class);
    }

    public function orderIngredients(): BelongsToMany
    {
        return $this->belongsToMany(OrderIngredient::class);
    }
}
