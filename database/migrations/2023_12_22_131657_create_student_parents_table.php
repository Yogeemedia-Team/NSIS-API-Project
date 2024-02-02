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
        Schema::connection('student_service')->create('student_parents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('organization_id');
            $table->string('student_id');
            $table->string('sp_father_first_name');
            $table->string('sp_father_last_name');
            $table->string('sp_father_nic');
            $table->string('sp_father_higher_education_qualification');
            $table->string('sp_father_occupation');
            $table->text('sp_father_official_address');
            $table->text('sp_father_permanent_address');
            $table->string('sp_father_contact_official');
            $table->string('sp_father_contact_mobile');
            $table->string('sp_mother_first_name');
            $table->string('sp_mother_last_name');
            $table->string('sp_mother_nic');
            $table->string('sp_mother_higher_education_qualification');
            $table->string('sp_mother_occupation');
            $table->text('sp_mother_official_address');
            $table->text('sp_mother_permanent_address');
            $table->string('sp_mother_contact_official');
            $table->string('sp_mother_contact_mobile');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_parents');
    }
};
