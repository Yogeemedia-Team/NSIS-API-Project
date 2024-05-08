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
        Schema::connection('student_service')->create('student_documents', function (Blueprint $table){
            $table->id();
            $table->unsignedBigInteger('organization_id');
            $table->string('student_id');
            $table->string('sd_profile_picture')->nullable();
            $table->string('sd_birth_certificate')->nullable();
            $table->string('sd_nic_father')->nullable();
            $table->string('sd_nic_mother')->nullable();
            $table->string('sd_marriage_certificate')->nullable();
            $table->string('sd_permission_letter')->nullable();
            $table->string('sd_leaving_certificate')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_documents');
    }
};
