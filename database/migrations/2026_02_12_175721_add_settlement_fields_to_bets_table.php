<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bets', function (Blueprint $table) {
            $table->decimal('payout_multiplier', 8, 2)->nullable();
            $table->decimal('payout_amount', 12, 2)->nullable();
            $table->decimal('profit', 12, 2)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('bets', function (Blueprint $table) {
            $table->dropColumn([
                'payout_multiplier',
                'payout_amount',
                'profit'
            ]);
        });
    }
};
