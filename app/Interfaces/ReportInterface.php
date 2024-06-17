<?php

namespace App\Interfaces;

use Illuminate\Contracts\Pagination\Paginator;

interface ReportInterface {
    public function payment_report(array $data);
    public function transaction_report(array $data);
    public function outstanding_report(array $data);
    public function payment_delaied_report(array $data);
    public function grade_class_student_report(array $data);
    public function student_extra_curriculars(array $data);
    public function income_report(array $data);
    
    
    
    
}