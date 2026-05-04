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
        Schema::create('rosters', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();

            $table->foreignId('event_id')
                ->constrained()
                ->onDelete('cascade');

            $table->foreignId('organization_id')
                ->constrained()
                ->onDelete('cascade');

            $table->foreignId('coach_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->enum('status', ['draft', 'ready', 'checked-in'])
                ->default('draft');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rosters');
    }
};