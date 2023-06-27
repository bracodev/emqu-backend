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
        Schema::create('terminals_statistics', function (Blueprint $table) {
            $table->id();

            $table->foreignId('terminal_id')->references('id')->on('terminals');

            $table->string('transmitted')->nullable();
            $table->string('received')->nullable();
            $table->string('loss')->nullable();
            $table->string('time')->nullable();
            $table->boolean('status')->nullable();
            $table->longText('logs');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('terminals_statistics');
    }
};
