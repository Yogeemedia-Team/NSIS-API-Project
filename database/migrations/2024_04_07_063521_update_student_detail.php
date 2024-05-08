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
        Schema::connection('student_service')->table('student_details', function (Blueprint $table) {
            $table->date('sd_promote_started_date')->nullable();
            $table->string('sd_name_in_full')->nullable()->change();
            $table->string('sd_gender')->nullable()->change();
            $table->string('sd_date_of_birth')->nullable()->change();
            $table->string('sd_religion')->nullable()->change();
            $table->string('sd_ethnicity')->nullable()->change();
            $table->string('sd_birth_certificate_number')->nullable()->change();
            $table->string('sd_admission_date')->nullable()->change();
            $table->string('sd_admission_payment_amount')->nullable()->change();
            $table->string('sd_no_of_installments')->nullable()->change();
        });

        Schema::connection('student_service')->table('student_parents', function (Blueprint $table) {
            $table->string('sp_father_first_name')->nullable()->change();
            $table->string('sp_father_last_name')->nullable()->change();
            $table->string('sp_father_nic')->nullable()->change();
            $table->string('sp_father_higher_education_qualification')->nullable()->change();
            $table->string('sp_father_occupation')->nullable()->change();
            $table->string('sp_mother_nic')->nullable()->change();
            $table->string('sp_mother_higher_education_qualification')->nullable()->change();
        });
    
        Schema::connection('student_service')->table('student_siblings', function (Blueprint $table) {
            $table->string('ss_details')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
