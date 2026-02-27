    <?php

    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    return new class extends Migration
    {
        public function up(): void
        {
            Schema::create('team_members', function (Blueprint $table) {
                $table->engine = 'InnoDB';
                $table->id();
                $table->unsignedBigInteger('team_id');
                $table->unsignedBigInteger('player_id');
                $table->timestamps();

                $table->foreign('team_id')->references('id')->on('teams')->onDelete('cascade');
                $table->foreign('player_id')->references('id')->on('players')->onDelete('cascade');
            });
        }

        public function down(): void
        {
            Schema::dropIfExists('team_members');
        }
    };
