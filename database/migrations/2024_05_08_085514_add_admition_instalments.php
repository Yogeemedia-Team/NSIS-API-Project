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
        Schema::connection('student_service')->create('admition_instalments', function (Blueprint $table) {
            $table->id();
            $table->string('admission_table_id');
            $table->string('admission_no');
            $table->float('instalment_amount', 10, 2);
            $table->integer('instalments_no');
            $table->string('reference_no')->nullable();
            $table->date('due_date');
            $table->date('paid_date')->nullable();
            $table->integer('status');
            $table->string('updated_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admition_instalments');
    }
};
