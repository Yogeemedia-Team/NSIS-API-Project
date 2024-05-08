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
        Schema::connection('student_service')->create('student_promote_history', function (Blueprint $table) {
            $table->id();
            $table->integer('student_id');
            $table->integer('promote_history_id');
            $table->float('monthly_fee');
            $table->integer('st_monthly_fee_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_promote_history');
    }
};
