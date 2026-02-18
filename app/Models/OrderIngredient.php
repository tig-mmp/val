<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderIngredient extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'order_id',
        'ingredient_id',
    ];
    
}
