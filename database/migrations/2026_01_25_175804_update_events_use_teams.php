<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {

            // eliminar columnas viejas (si existen)
            if (Schema::hasColumn('events', 'team_a_name')) {
                $table->dropColumn('team_a_name');
            }

            if (Schema::hasColumn('events', 'team_b_name')) {
                $table->dropColumn('team_b_name');
            }

            if (Schema::hasColumn('events', 'team_a_logo')) {
                $table->dropColumn('team_a_logo');
            }

            if (Schema::hasColumn('events', 'team_b_logo')) {
                $table->dropColumn('team_b_logo');
            }

            // agregar referencias a teams (si no existen)
            if (!Schema::hasColumn('events', 'team_a_id')) {
                $table->unsignedBigInteger('team_a_id')->after('title');
            }

            if (!Schema::hasColumn('events', 'team_b_id')) {
                $table->unsignedBigInteger('team_b_id')->after('team_a_id');
            }
        });
    }

    public function down(): void
    {
        // no rollback por ahora
    }
};
