<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->unsignedSmallInteger('team_a_score')->nullable()->after('result');
            $table->unsignedSmallInteger('team_b_score')->nullable()->after('team_a_score');
            $table->string('match_status_short')->nullable()->after('team_b_score');
            $table->string('match_status_long')->nullable()->after('match_status_short');
            $table->timestamp('score_updated_at')->nullable()->after('match_status_long');
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn([
                'team_a_score',
                'team_b_score',
                'match_status_short',
                'match_status_long',
                'score_updated_at',
            ]);
        });
    }
};
