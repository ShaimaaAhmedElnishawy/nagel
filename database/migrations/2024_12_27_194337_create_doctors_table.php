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
            $table->string('photo')->nullable();
            $table->enum('status',['pending','approved','rejected'])->default('pending');
            $table->float('rating', 2, 1)->nullable(); // last rating given
            $table->float('total_ratings', 3, 2)->default(0); // average rating
            $table->integer('number_of_ratings')->default(0);
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
