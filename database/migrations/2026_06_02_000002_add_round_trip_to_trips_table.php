<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('trips', function (Blueprint $table) {
            $table->boolean('is_round_trip')->default(false)->after('notes');
            $table->timestamp('return_date')->nullable()->after('is_round_trip');
            $table->decimal('daily_budget_limit', 12, 2)->nullable()->after('budget_amount');
        });
    }

    public function down(): void
    {
        Schema::table('trips', function (Blueprint $table) {
            $table->dropColumn(['is_round_trip', 'return_date', 'daily_budget_limit']);
        });
    }
};
