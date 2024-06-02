<?php

namespace App\Repositories;
use Exception;
use App\Models\AccountPayable;
use App\Models\StudentPayment;
use App\Models\StudentDetail;
use App\Models\YearGradeClass;
use App\Interfaces\ReportInterface;
use App\Models\StudentExtraCurricular;
use App\Models\Invoice;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class ReportRepository implements ReportInterface{

    public function payment_report( array $data) {
     

        $studentPaymentquery = StudentPayment::select("*");
        if(array_key_exists("admission_no", $data) &&  $data['admission_no'] != null ){
            $studentPaymentquery->where('admission_no', $data['admission_no']);
        }
       
        if(array_key_exists("date", $data) &&  $data['date'] != null ){
            $studentPaymentquery->where('due_date','<=',$data['date']);
        }
        if(array_key_exists("from_date", $data) &&  $data['from_date'] != null &&
                array_key_exists("to_date", $data) &&  $data['to_date'] != null){
            try {
                $startDate = Carbon::parse($data['from_date']); 
                $endDate = Carbon::parse($data['to_date']);
                $studentPaymentquery->whereBetween('created_at', [$startDate, $endDate]);
            } catch (\Throwable $th) {
                
            }
    
        }else if(array_key_exists("from_date", $data) &&  $data['from_date'] != null  ){
            try {
                $startDate = Carbon::parse($data['from_date']); 
                $studentPaymentquery->where('created_at','>=', $startDate);
                
            } catch (\Throwable $th) {
                
            }
        }else if(array_key_exists("to_date", $data) &&  $data['to_date'] != null ){
            try {
                $endDate = Carbon::parse($data['to_date']);
                $studentPaymentquery->where('created_at','<=', $endDate);
            } catch (\Throwable $th) {
                
            }
        }
        
        // Assuming you have a direct relationship between StudentDetail and Invoice
        $studentPayments = $studentPaymentquery->get();
        if (empty( $studentPayments)) {
            throw new Exception("Payment details not found", Response::HTTP_NOT_FOUND);
        }
        
        return  $studentPayments;
     
    }

    public function transaction_report(array $data) {
         

        $studentPaymentquery = StudentPayment::select("*")->with("accountPayable");
        if(array_key_exists("admission_no", $data) &&  $data['admission_no'] != null ){
            $studentPaymentquery->where('admission_no', $data['admission_no']);
        }
       
        if(array_key_exists("date", $data) &&  $data['date'] != null ){
            $studentPaymentquery->where('due_date','<=',$data['date']);
        }
        if(array_key_exists("from_date", $data) &&  $data['from_date'] != null &&
                array_key_exists("to_date", $data) &&  $data['to_date'] != null){
            try {
                $startDate = Carbon::parse($data['from_date']); 
                $endDate = Carbon::parse($data['to_date']);
                $studentPaymentquery->whereBetween('created_at', [$startDate, $endDate]);
            } catch (\Throwable $th) {
                
            }
    
        }else if(array_key_exists("from_date", $data) &&  $data['from_date'] != null  ){
            try {
                $startDate = Carbon::parse($data['from_date']); 
                $studentPaymentquery->where('created_at','>=', $startDate);
                
            } catch (\Throwable $th) {
                
            }
        }else if(array_key_exists("to_date", $data) &&  $data['to_date'] != null ){
            try {
                $endDate = Carbon::parse($data['to_date']);
                $studentPaymentquery->where('created_at','<=', $endDate);
            } catch (\Throwable $th) {
                
            }
        }
        
        // Assuming you have a direct relationship between StudentDetail and Invoice
        $studentPayments = $studentPaymentquery->get();
        foreach ($studentPayments as $key => $value) {
            $invoiceIds = [];
            $decodedIds = json_decode($value->invoice_id, true);
            if (is_array($decodedIds)) {
                $invoiceIds = array_merge($invoiceIds, $decodedIds);
            }
            $query = Invoice::select("*")->with("accountPaybles")
                    ->whereIn('invoice_number', $invoiceIds)
                    ->get();
            $studentPayments[$key]->invoice_list = $query;
        }
        if (empty( $studentPayments)) {
            throw new Exception("Payment details not found", Response::HTTP_NOT_FOUND);
        }
        
        return  $studentPayments;
     
    }
    public function outstanding_report(array $data) {
         

        $query = Invoice::select("*")->with("accountPaybles");
        if(array_key_exists("admission_no", $data) &&  $data['admission_no'] != null ){
            $query->where('admission_no', $data['admission_no'])->whereIn('status', [0, 2]);
        }
        if(array_key_exists("from_date", $data) &&  $data['from_date'] != null &&
            array_key_exists("to_date", $data) &&  $data['to_date'] != null){
            try {
                $startDate = Carbon::parse($data['from_date']); 
                $endDate = Carbon::parse($data['to_date']);
                $query->whereBetween('created_at', [$startDate, $endDate]);
            } catch (\Throwable $th) {
                
            }

        }else if(array_key_exists("from_date", $data) &&  $data['from_date'] != null  ){
            try {
                $startDate = Carbon::parse($data['from_date']); 
                $query->where('created_at','>=', $startDate);
                
            } catch (\Throwable $th) {
                
            }
        }else if(array_key_exists("to_date", $data) &&  $data['to_date'] != null ){
            try {
                $endDate = Carbon::parse($data['to_date']);
                $query->where('created_at','<=', $endDate);
            } catch (\Throwable $th) {
                
            }
        }
        
        // Assuming you have a direct relationship between StudentDetail and Invoice
        $studentinvoices = $query->get();
        if (empty( $studentinvoices)) {
            throw new Exception("Invoice details not found", Response::HTTP_NOT_FOUND);
        }
        
        return  $studentinvoices;
     
    }

    public function payment_delaied_report(array $data) {
         
        $student = StudentDetail::select('student_details.*')->with("yearGradeClass")
        ->join('invoices', 'invoices.admission_no', '=', 'student_details.sd_admission_no')
        ->whereIn('invoices.status', [0, 2])
        ->get();

        if (empty($student)) {
            throw new Exception("User student does not exist.", Response::HTTP_NOT_FOUND);
        }

        return $student;
    
    }

    
    public function grade_class_student_report(array $data) {
         
        $studentquery = YearGradeClass::select('id');
        if(array_key_exists("year", $data) &&  $data['year'] != null ){
            $studentquery->where('year', $data['year']);
        }
        if(array_key_exists("master_grade_id", $data) &&  $data['master_grade_id'] != null ){
            $studentquery->where('master_grade_id', $data['master_grade_id']);
        }
        if(array_key_exists("master_class_id", $data) &&  $data['master_class_id'] != null ){
            $studentquery->where('master_class_id', $data['master_class_id']);
        }
       
       
        // Get the list of YearGradeClass IDs
        $studentYear = $studentquery->pluck('id')->toArray();

        // Check if the list is empty and throw an exception if necessary
        if (empty($studentYear) || count($studentYear) == 0) {
            throw new Exception("Students not found.", Response::HTTP_NOT_FOUND);
        }

        // Use the list of YearGradeClass IDs to query the StudentDetail model
        $students = StudentDetail::whereIn('sd_year_grade_class_id', $studentYear)->get();

        if (empty($students)) {
            throw new Exception("User student does not exist.", Response::HTTP_NOT_FOUND);
        }

        return $students;
    
    }


    public function student_extra_curriculars(array $data) {
         
        $studentCurricularquery = StudentExtraCurricular::select('student_id');
        if(array_key_exists("extra_curricular_id", $data) &&  $data['extra_curricular_id'] != null ){
            $studentCurricularquery->where('extra_curricular_id', $data['extra_curricular_id']);
        }
       
       
       
        // Get the list of YearGradeClass IDs
        $CurricularstudentIds= $studentCurricularquery->pluck('student_id')->toArray();

        // Check if the list is empty and throw an exception if necessary
        if (empty($CurricularstudentIds) || count($CurricularstudentIds) == 0) {
            throw new Exception("Students not found.", Response::HTTP_NOT_FOUND);
        }

        // Use the list of YearGradeClass IDs to query the StudentDetail model
        $students = StudentDetail::whereIn('student_id', $CurricularstudentIds)->get();

        if (empty($students)) {
            throw new Exception("Student not found.", Response::HTTP_NOT_FOUND);
        }

        return $students;
    
    }

    
    public function income_report(array $data) {
         
        $studentPaymentquery = StudentPayment::select("*");
       
        if(array_key_exists("from_date", $data) &&  $data['from_date'] != null &&
                array_key_exists("to_date", $data) &&  $data['to_date'] != null){
            try {
                $startDate = Carbon::parse($data['from_date']); 
                $endDate = Carbon::parse($data['to_date']);
                $studentPaymentquery->whereBetween('created_at', [$startDate, $endDate]);
            } catch (\Throwable $th) {
                
            }
    
        }else if(array_key_exists("from_date", $data) &&  $data['from_date'] != null  ){
            try {
                $startDate = Carbon::parse($data['from_date']); 
                $studentPaymentquery->where('created_at','>=', $startDate);
                
            } catch (\Throwable $th) {
                
            }
        }else if(array_key_exists("to_date", $data) &&  $data['to_date'] != null ){
            try {
                $endDate = Carbon::parse($data['to_date']);
                $studentPaymentquery->where('created_at','<=', $endDate);
            } catch (\Throwable $th) {
                
            }
        }
        
        // Assuming you have a direct relationship between StudentDetail and Invoice
        $studentPayments = $studentPaymentquery->get();
        if (empty( $studentPayments)) {
            throw new Exception("Payment details not found", Response::HTTP_NOT_FOUND);
        }
        
        return  $studentPayments;
    
    }
    

}


