<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->string('receipt_path')->nullable()->after('location_name');
            $table->boolean('is_recurring')->default(false)->after('receipt_path');
            $table->string('recurring_interval', 20)->nullable()->after('is_recurring');
            $table->foreignId('parent_expense_id')->nullable()->after('recurring_interval')
                ->constrained('expenses')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->dropForeign(['parent_expense_id']);
            $table->dropColumn(['receipt_path', 'is_recurring', 'recurring_interval', 'parent_expense_id']);
        });
    }
};
