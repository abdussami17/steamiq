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
        Schema::create('tournament_matches', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->unsignedBigInteger('tournament_id');
            $table->unsignedBigInteger('team_a_id')->nullable();
            $table->unsignedBigInteger('team_b_id')->nullable();
            $table->unsignedBigInteger('winner_team_id')->nullable();
            $table->integer('round_no')->default(1);
            $table->enum('status',['pending','completed'])->default('pending');
            $table->string('game_title')->nullable();
            $table->string('format')->default('single'); // single/bo3/bo5/custom
            $table->dateTime('scheduled_at')->nullable();
            $table->timestamps();

            $table->foreign('tournament_id')->references('id')->on('tournaments')->onDelete('cascade');
            $table->foreign('team_a_id')->references('id')->on('teams')->onDelete('set null');
            $table->foreign('team_b_id')->references('id')->on('teams')->onDelete('set null');
            $table->foreign('winner_team_id')->references('id')->on('teams')->onDelete('set null');
            $table->string('pin', 10)->nullable();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tournament_matches');
    }
};
