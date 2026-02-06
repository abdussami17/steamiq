<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('teams', function (Blueprint $table) {
            $table->integer('seed_rank')->nullable()->after('team_name');
        });
    }
    
    public function down(): void
    {
        Schema::table('teams', function (Blueprint $table) {
            $table->dropColumn('seed_rank');
        });
    }
    
};
