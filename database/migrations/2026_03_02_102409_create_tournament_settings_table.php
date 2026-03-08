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
        Schema::create('tournament_settings', function (Blueprint $t) {
            $t->engine = 'InnoDB';
            $t->id();
            $t->foreignId('event_id')->constrained()->cascadeOnDelete();
        
            $t->boolean('brain_enabled')->default(false);
            $t->string('brain_type')->nullable();
            $t->integer('brain_score')->nullable();
      
        
            $t->string('game')->nullable();
            $t->integer('players_per_team')->nullable();
            $t->string('match_rule')->nullable();
        
            $t->integer('points_win')->default(0);
            $t->integer('points_draw')->default(0);
        
            $t->string('tournament_type')->nullable();
            $t->integer('number_of_teams')->nullable();
        
            $t->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tournament_settings');
    }
};
