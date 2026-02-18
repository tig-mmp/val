<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('order_ingredients', function (Blueprint $table): void {
            $table->id();
            $table->timestamps();

            $table->foreignId('order_id')->constrained();
            $table->index('order_id');

            $table->foreignId('ingredient_id')->constrained();
            $table->index('ingredient_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_ingredients');
    }
};
