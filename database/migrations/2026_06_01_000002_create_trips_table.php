<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trips', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('vehicle_id')->constrained()->restrictOnDelete();
            $table->string('name');
            $table->decimal('budget_amount', 12, 2);
            $table->string('origin_name');
            $table->double('origin_lat');
            $table->double('origin_lng');
            $table->string('destination_name');
            $table->double('destination_lat');
            $table->double('destination_lng');
            $table->float('distance_km')->nullable();
            $table->integer('duration_minutes')->nullable();
            $table->decimal('estimated_fuel_cost', 12, 2)->nullable();
            $table->longText('route_geometry')->nullable();
            $table->string('status', 20)->default('planning');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trips');
    }
};
