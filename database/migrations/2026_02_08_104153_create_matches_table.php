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
        Schema::create('matches', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
        
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
        
            $table->string('match_name');
        
            $table->foreignId('team_a_id')->constrained('teams')->cascadeOnDelete();
            $table->foreignId('team_b_id')->constrained('teams')->cascadeOnDelete();
        
            $table->string('game_title')->nullable();
        
            $table->enum('format', ['single','bo3','bo5','custom']);
            $table->integer('win_required')->default(1);
        
            $table->date('date');
            $table->time('time');
        
            $table->string('pin')->nullable();
        
            $table->foreignId('winner_team_id')->nullable()->constrained('teams')->nullOnDelete();
        
            $table->enum('status',['scheduled','live','completed'])->default('scheduled');
      
            $table->timestamps();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('matches');
    }
};
