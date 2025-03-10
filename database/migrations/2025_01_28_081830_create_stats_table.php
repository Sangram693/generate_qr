<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stats', function (Blueprint $table) {
            $table->id();
            $table->string('product_type');
            $table->string('product_id'); 
            $table->integer('total_hits')->default(0);
            $table->integer('unique_hits')->default(0);
            $table->timestamps();

            $table->unique(['product_type', 'product_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stats');
    }
};
