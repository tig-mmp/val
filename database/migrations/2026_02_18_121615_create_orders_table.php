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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->index('user_id');

            $table->timestamps();

            // TODO set english names in the database
            $table->enum('size', ['Individual', 'Média', 'Grande', 'Familiar'])
                ->default('Individual');
            $table->string('base')->max(255);
            $table->string('state')->max(255);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
