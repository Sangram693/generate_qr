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
        Schema::create('dealers', function (Blueprint $table) {
            $table->id(); 
            $table->string('dealer_id')->unique(); 
            $table->string('dealer_name');
            $table->string('dealer_phone')->unique();
            $table->string('dealer_email')->unique();
            $table->string('user_name')->unique();
            $table->string('password'); 
            $table->string('location')->nullable(); 
            $table->boolean('is_verify')->default(false); 
            $table->unsignedBigInteger('verified_by')->nullable();
            $table->foreign('verified_by')->references('id')->on('users')->onDelete('set null');
            $table->timestamps(); 
            $table->integer('status')->default(1); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dealers');
    }
};
