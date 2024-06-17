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
        Schema::connection('student_service')->create('student_extra_curricular', function (Blueprint $table) {
            $table->id();
            $table->integer('student_id');
            $table->integer('extra_curricular_id');
            $table->date('start_from')->nullable();
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
        Schema::connection('student_service')->dropIfExists('student_extra_curricular');
    }
};
