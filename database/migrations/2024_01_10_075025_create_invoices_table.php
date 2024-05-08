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
        Schema::connection('student_service')->create('invoices', function (Blueprint $table) {
                $table->id();
                $table->string('invoice_number'); // Change the data type to string
                $table->string('admission_no');
                $table->date('due_date');
                $table->float('invoice_total');
                $table->float('total_paid');
                $table->float('total_due');
                $table->integer('status');
                $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
