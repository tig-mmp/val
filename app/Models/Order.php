<?php

namespace App\Models;

use Database\Factories\OrderFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    /** @use HasFactory<OrderFactory> */
    use HasFactory;

    public const array SIZES = ['Individual', 'Média', 'Grande', 'Familiar'];

    public const array STATES = ['Pendente', 'Concluído', 'Cancelado'];

    public const string STATE_COMPLETE = 'Concluído';

    public const string STATE_PENDING = 'Pendente';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'size',
        'base',
        'state',
    ];

    public function ingredients(): HasMany
    {
        return $this->hasMany(OrderIngredient::class);
    }

    public function orderIngredients(): HasMany
    {
        return $this->hasMany(OrderIngredient::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
