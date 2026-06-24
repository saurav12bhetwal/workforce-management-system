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
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('department_id')
                ->nullable()
                ->after('email')
                ->constrained()
                ->nullOnDelete();

            $table->foreignId('manager_id')
                ->nullable()
                ->after('department_id')
                ->constrained('users')
                ->nullOnDelete();
            $table->foreignId('designation_id')
                ->nullable()
                ->after('manager_id')
                ->constrained()
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['department_id']);
            $table->dropForeign(['manager_id']);

            $table->dropColumn([
                'department_id',
                'manager_id'
            ]);
        });
    }
};
