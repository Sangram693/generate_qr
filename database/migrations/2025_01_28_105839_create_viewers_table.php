<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('viewers', function (Blueprint $table) {
            $table->id();
            $table->string('product_type'); 
            $table->string('product_id'); 
            $table->string('advertising_id')->nullable(); 
            $table->string('ip_address')->nullable(); 
            $table->string('user_agent')->nullable(); 
            $table->string('city')->nullable(); 
            $table->string('country')->nullable(); 
            $table->timestamp('first_seen')->useCurrent(); 
            $table->timestamp('last_seen')->useCurrent()->useCurrentOnUpdate(); 
            $table->timestamps();

            $table->unique(['product_type', 'product_id', 'advertising_id', 'ip_address']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('viewers');
    }
};

