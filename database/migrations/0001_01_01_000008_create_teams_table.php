<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teams', function (Blueprint $t) {
            $t->engine = 'InnoDB';
            $t->id();
            $t->foreignId('sub_group_id')->nullable()->constrained()->cascadeOnDelete();
            $t->foreignId('group_id')->constrained()->cascadeOnDelete();
            $t->string('name');
            $t->string('profile')->nullable();
            $t->enum('division',['Primary','Junior'])->nullable(); 
        
            $t->timestamps();
         
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teams');
    }
};
