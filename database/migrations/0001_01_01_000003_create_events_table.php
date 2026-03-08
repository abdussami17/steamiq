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
        Schema::create('events', function (Blueprint $t) {
            $t->engine = 'InnoDB';
            $t->id();
            $t->string('name');
            $t->enum('type',['esports','xr']);
            $t->string('location');
            $t->date('start_date');
            $t->date('end_date');
            $t->enum('status',['draft','live','closed'])->default('draft');
            $t->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
