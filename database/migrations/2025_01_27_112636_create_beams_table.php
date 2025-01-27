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
        Schema::create('beams', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('model_no')->nullable();
            $table->string('bach_no')->nullable();
            $table->string('serial_no')->nullable();
            $table->string('mfd_origin')->nullable();
            $table->string('mfd_date')->nullable();
            $table->string('asp')->nullable();
            $table->boolean('status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('beams');
    }
};
