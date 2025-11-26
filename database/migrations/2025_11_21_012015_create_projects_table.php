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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('summary');

            $table->json('objectives')->nullable();     // store as JSON array
            $table->date('duration_start')->nullable();
            $table->date('duration_end')->nullable();

            $table->text('target_areas')->nullable();
            $table->text('target_groups')->nullable();
            $table->string('donor')->nullable();

            $table->json('key_activities')->nullable(); // store as JSON array

            $table->json('images')->nullable();   // image file path in storage
            $table->string('department')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
