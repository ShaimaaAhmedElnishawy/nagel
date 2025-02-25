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
        Schema::create('diagnosis', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('patient_id');
            // $table->foreign('patient_id')
            // ->references('id')
            // ->on('patients')
            // ->onDelete('cascade')->onUpdate('cascade');
            
            $table->unsignedBigInteger('image_id');
            $table->foreign('image_id')
            ->references('id')
            ->on('nail_images')
            ->onDelete('cascade')->onUpdate('cascade');
            
            $table->unsignedBigInteger('disease_id');
            $table->foreign('disease_id')
            ->references('id')
            ->on('diseases')
            ->onDelete('cascade')->onUpdate('cascade');
            $table->timestamp('date');
            $table->float('percentage');
            $table->text('notes');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('diagnosis');
    }
};
