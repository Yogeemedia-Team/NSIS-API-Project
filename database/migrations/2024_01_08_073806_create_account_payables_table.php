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
        Schema::connection('student_service')->create('account_payables', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number');
            $table->string('admission_no');
            $table->float('amount');
            $table->string('type')->comment('surcharge or monthly');
            $table->date('due_date');
            $table->float('outstanding_balance');
            $table->integer('eligibility')->comment('1 = true / 0 = false');
            $table->integer('status')->comment('1 = paid / 0 = unpaid');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('account_payables');
    }
};
