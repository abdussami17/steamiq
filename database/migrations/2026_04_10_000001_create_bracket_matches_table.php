<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bracket_matches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->string('pod')->nullable();        // group pod name e.g. "Red Pod"
            $table->string('division')->nullable();   // 'Primary', 'Junior', or null for cross
            $table->string('phase');                  // 'qualification', 'pod_semifinal', 'grand_final'
            $table->unsignedInteger('round_no');      // 1 = first round
            $table->unsignedInteger('match_no');      // position within round (0-indexed)
            $table->foreignId('team_a_id')->nullable()->constrained('teams')->nullOnDelete();
            $table->foreignId('team_b_id')->nullable()->constrained('teams')->nullOnDelete();
            $table->integer('team_a_score')->nullable();
            $table->integer('team_b_score')->nullable();
            $table->foreignId('winner_team_id')->nullable()->constrained('teams')->nullOnDelete();
            $table->enum('status', ['pending', 'completed'])->default('pending');
            $table->unsignedBigInteger('next_match_id')->nullable(); // FK set after insert
            $table->boolean('next_is_team_a')->nullable();           // winner goes to slot A or B
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bracket_matches');
    }
};
