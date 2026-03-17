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
        Schema::create('challenge_activities', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->string('name')->nullable();
            $table->integer('max_score')->default(0);
            $table->string('activity_or_mission')->nullable();
            $table->string('activity_type')->nullable(); // brain|esports|egaming|playground
            $table->string('badge_name')->nullable();
    
            // brain
            $table->string('brain_type')->nullable();
            $table->string('brain_description')->nullable();
            $table->string('point_structure')->nullable(); // per_team | per_player
    
            // esports
            $table->string('esports_type')->nullable();
            $table->string('esports_players')->nullable();
            $table->string('esports_structure')->nullable();
            $table->string('esports_description')->nullable();
    
            // egaming
            $table->string('egaming_type')->nullable();
            $table->string('egaming_mode')->nullable();
            $table->string('egaming_structure')->nullable();
            $table->string('egaming_description')->nullable();
    
            // playground
            $table->string('playground_description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('challenge_activities');
    }
};
