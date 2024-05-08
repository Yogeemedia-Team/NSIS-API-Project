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
        Schema::connection('student_service')->create('student_monthly_fee', function (Blueprint $table) {
            $table->id();
            $table->string('student_id');
            $table->integer('sd_year_grade_class_id');
            $table->float('monthly_fee');
            $table->date('start_from');
            $table->date('end_from')->nullable();
            $table->integer('status')->comment('1 = active / 0 = inActive');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_monthly_fee');
    }
};
