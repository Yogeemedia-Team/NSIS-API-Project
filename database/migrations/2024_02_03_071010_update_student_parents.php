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
       Schema::connection('student_service')->table('student_parents', function (Blueprint $table) {
            // Update 'name' column to allow nullable
            $table->string('sp_father_official_address')->nullable()->change();
            $table->string('sp_father_permanent_address')->nullable()->change();
            $table->string('sp_father_contact_official')->nullable()->change();
            $table->string('sp_father_contact_mobile')->nullable()->change();
            $table->string('sp_mother_occupation')->nullable()->change();
            $table->string('sp_mother_official_address')->nullable()->change();
            $table->string('sp_mother_permanent_address')->nullable()->change();
            $table->string('sp_mother_contact_official')->nullable()->change();
            $table->string('sp_mother_contact_mobile')->nullable()->change();
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
