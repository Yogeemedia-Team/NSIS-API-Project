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
        Schema::connection('student_service')->create('enrollments', function (Blueprint $table) {
            $table->id();
            $table->string('admission_id');
            $table->integer('grade_class_id');
            $table->string('first_name');
            $table->string('last_name')->nullable();
            $table->string('name_with_initials');
            $table->string('name_in_full');
            $table->string('address_line1');
            $table->string('address_line2')->nullable();
            $table->string('address_city');
            $table->string('telephone_residence')->nullable();
            $table->string('telephone_mobile');
            $table->string('telephone_whatsapp')->nullable();
            $table->string('email_address');
            $table->string('sex');
            $table->date('date_of_birth');
            $table->string('religion')->nullable();
            $table->string('ethnicity')->nullable();
            $table->string('birthcertificate_number');
            $table->string('profle_picture_path')->nullable();
            $table->text('health_conditions')->nullable();
            $table->date('applied_date');
            $table->string('admission_status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enrollments');
    }
};
