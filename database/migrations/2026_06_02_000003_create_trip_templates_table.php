<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trip_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('origin_name');
            $table->double('origin_lat');
            $table->double('origin_lng');
            $table->string('destination_name');
            $table->double('destination_lat');
            $table->double('destination_lng');
            $table->decimal('default_budget', 12, 2)->nullable();
            $table->string('default_vehicle_type', 20)->nullable();
            $table->json('waypoints_json')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trip_templates');
    }
};
