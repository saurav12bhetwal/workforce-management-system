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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->date('attendance_date');

            $table->timestamp('check_in')
                ->nullable();

            $table->timestamp('check_out')
                ->nullable();

            $table->decimal('check_in_lat', 10, 7)
                ->nullable();

            $table->decimal('check_in_lng', 10, 7)
                ->nullable();

            $table->decimal('check_out_lat', 10, 7)
                ->nullable();

            $table->decimal('check_out_lng', 10, 7)
                ->nullable();

            $table->unsignedInteger('working_minutes')->nullable()->default(null);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
