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
       Schema::connection('student_service')->create('student_details', function (Blueprint $table) {
            $table->id();
            $table->string('student_id')->unique();
            $table->unsignedBigInteger('organization_id');
            $table->string('sd_year_grade_class_id');
            $table->string('sd_admission_no')->unique();
            $table->string('sd_first_name');
            $table->string('sd_last_name');
            $table->string('sd_name_with_initials');
            $table->string('sd_name_in_full');
            $table->string('sd_address_line1');
            $table->string('sd_address_line2')->nullable();
            $table->string('sd_address_city');
            $table->string('sd_telephone_residence')->nullable();
            $table->string('sd_telephone_mobile')->nullable();
            $table->string('sd_telephone_whatsapp')->nullable();
            $table->string('sd_email_address')->nullable();
            $table->string('sd_gender');
            $table->date('sd_date_of_birth');
            $table->string('sd_religion');
            $table->string('sd_ethnicity');
            $table->string('sd_birth_certificate_number');
            $table->string('sd_profile_picture')->nullable();
            $table->text('sd_health_conditions')->nullable();
            $table->date('sd_admission_date');
            $table->decimal('sd_admission_payment_amount', 10, 2);
            $table->integer('sd_no_of_installments');
            $table->integer('sd_admission_status')->default(0);
            $table->decimal('sd_school_fee', 10, 2)->nullable();
            $table->decimal('sd_total_due', 10, 2)->nullable();
            $table->string('sd_payment_status')->default(0);
            $table->string('sd_academic_status')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_details');
    }
};
