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
        Schema::table('nail_images', function (Blueprint $table) {
            //
            $table->unsignedBigInteger('diseases_id')->nullable();
            $table->foreign('diseases_id')
            ->references('id')
            ->on('diseases')
            ->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('nail_images', function (Blueprint $table) {
            //
        });
    }
};
