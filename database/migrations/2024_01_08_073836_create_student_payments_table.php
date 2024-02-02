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
        Schema::connection('student_service')->create('student_payments', function (Blueprint $table) {
            $table->id();
            $table->string('payment_id');
            $table->string('admission_no');
            $table->json('invoice_id');
            $table->date('date');
            $table->date('due_date');
            $table->float('total_due', 10, 2);
            $table->integer('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_payments');
    }
};
