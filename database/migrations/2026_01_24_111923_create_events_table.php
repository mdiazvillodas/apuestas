<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();

            $table->string('title'); // Ej: "Barcelona vs Real Madrid"

            $table->dateTime('betting_opens_at');
            $table->dateTime('betting_closes_at');
            $table->dateTime('starts_at');

            $table->enum('status', ['draft', 'open', 'closed', 'finished'])
                  ->default('draft');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};

