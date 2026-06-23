<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trip_routes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trip_id')->constrained()->cascadeOnDelete();
            $table->string('route_name', 100);
            $table->float('distance_km');
            $table->integer('duration_minutes');
            $table->decimal('estimated_fuel_cost', 12, 2);
            $table->longText('route_geometry')->nullable();
            $table->boolean('is_selected')->default(false);
            $table->text('route_summary')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trip_routes');
    }
};
