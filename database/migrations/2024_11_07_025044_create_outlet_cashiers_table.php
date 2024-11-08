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
        Schema::create('outlet_cashiers', function (Blueprint $table) {
            $table->id();
            $table->foreignId("cashier_id")->constrained("users","id")->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId("outlet_id")->constrained("outlets","id")->cascadeOnDelete()->cascadeOnUpdate();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('outlet_cashiers');
    }
};
