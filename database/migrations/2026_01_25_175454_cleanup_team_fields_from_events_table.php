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
    Schema::table('events', function (Blueprint $table) {
        $table->dropColumn([
            'team_a_name',
            'team_b_name',
            'team_a_logo',
            'team_b_logo',
        ]);
    });
}

public function down(): void
{
    Schema::table('events', function (Blueprint $table) {
        $table->string('team_a_name');
        $table->string('team_b_name');
        $table->string('team_a_logo')->nullable();
        $table->string('team_b_logo')->nullable();
    });
}

};
