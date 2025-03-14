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
        Schema::create('doctors', function (Blueprint $table) {
            $table->id();
            $table->string ('name');
            $table->string ('email')->unique();
            $table->string ('phone');
            $table->string ('password');
            $table->text ('specialization');
            $table->string('proof');
            $table->float('rateing', 2, 1)->default(5.0); // e.g., 4.5
            $table->integer('total_rateings')->default(5); // Optional: Track total rateings
            $table->timestamps();
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doctors');
    }
};
