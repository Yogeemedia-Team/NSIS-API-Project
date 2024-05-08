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
        
        Schema::connection('student_service')->create('promote_history', function (Blueprint $table) {
            $table->id();
            $table->integer('prev_sd_year_grade_class_id');
            $table->integer('promoted_sd_year_grade_class_id');
            $table->date('promoted_start_from');
            $table->date('promoted_end_from');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promote_history');
    }
};
