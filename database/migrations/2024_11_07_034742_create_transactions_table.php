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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId("outlet_id")->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId("member_id")->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId("package_id")->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId("cashier_id")->constrained("users","id")->cascadeOnDelete()->cascadeOnUpdate();
            $table->double("additional_costs")->default(0);
            $table->double("discount")->default(0);
            $table->double("tax")->default(11.0);
            $table->enum("status",["new","process","retrieved"])->default("new");
            $table->boolean("is_payed")->default(false);
            $table->datetime("deadline");
            $table->string("invoice_code");
            $table->double("quantity")->default(0);
            $table->double("total")->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
