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

            // ID externo del proveedor (fixture.id)
            $table->unsignedBigInteger('external_id')
                ->nullable()
                ->after('id')
                ->index();

            // Fuente del evento (manual | api)
            $table->string('source')
                ->default('manual')
                ->after('external_id');

            // Round (Group Stage - 1, Quarter-finals, etc.)
            $table->string('round')
                ->nullable()
                ->after('starts_at');

            // Indica si el evento es gestionado automáticamente
            $table->boolean('auto_managed')
                ->default(false)
                ->after('source');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {

            $table->dropColumn([
                'external_id',
                'source',
                'round',
                'auto_managed',
            ]);
        });
    }
};