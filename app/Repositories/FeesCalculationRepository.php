<?php

namespace App\Repositories;

use Exception;
use App\Models\AccountPayable;
use App\Models\StudentPayment;
use App\Models\StudentDetail;
use App\Interfaces\FeesCalculationInterface;
use App\Models\Invoice;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class FeesCalculationRepository implements FeesCalculationInterface {
    public function monthlyFee()
    {
        try {
            // Start a database transaction
            DB::beginTransaction();

            $currentDate = Carbon::now();
            $dueDateThreshold = $currentDate->copy()->subMonths(3);
            $dueDate = $currentDate->copy()->addMonth();

           
 
            // Fetch eligible students with details
            $monthlyPaymentEligibleLists = $this->getMonthlyPaymentEligibleLists();

            // Process surcharges and create records
            $this->processSurcharges($monthlyPaymentEligibleLists, $dueDate, $dueDateThreshold);

            // Commit the transaction
            DB::commit();

            return "All records inserted successfully!";
        } catch (\Exception $e) {
            // An error occurred, rollback the transaction
            DB::rollBack();

            // Log the exception
            Log::error("Error: " . $e->getMessage());

            // Return an error response
            return "Error: " . $e->getMessage();
        }
    }

    private function getMonthlyPaymentEligibleLists()
    {
        $studentDetails =  StudentDetail::where('sd_academic_status', 1)
            ->with('yearGradeClass')
            ->get();

            // foreach($studentDetails as $key => $studentDetail){
            //     $studentPayables = AccountPayable::where('admission_no', $studentDetail->sd_admission_no)
            //     ->where('due_date',">",Carbon::now()->format('Y-m-d'))
            //     ->first(); 
            //     if(!$studentPayables)  {
            //         unset($studentDetails[$key]);
            //     } 
            // }
            return $studentDetails ;
    }

private function generateInvoiceNumber() {
    $timestamp = time();
    $invoiceNumber = $timestamp;
    return $invoiceNumber;
}

private function processSurcharges($monthlyPaymentEligibleLists, $dueDate, $dueDateThreshold) {
    $processedUsers = []; // Array to store processed users
    $mainIteration = 0;

    foreach ($monthlyPaymentEligibleLists as $monthlyPaymentEligibleList) {
        $mainIteration++;
        // Generate a new unique ID and extract substring for invoice number
        
        $invoice_number = $this->generateInvoiceNumber(). $mainIteration;

        $surchageEligibilitys = $this->getSurchageEligibilitys($monthlyPaymentEligibleList, $dueDateThreshold);
        $count = $surchageEligibilitys->count();
        
        if($count == 0 ){
            $this->createMonthlyPaymentRecord($monthlyPaymentEligibleList, $dueDate, $invoice_number, 1);
        }if($count == 4 ){// count is 4 then no sercharge for this user due to the requrment
            $this->createMonthlyPaymentRecord($monthlyPaymentEligibleList, $dueDate, $invoice_number,0);
        }else{
            foreach ($surchageEligibilitys as $surchageEligibility) {
                $userId = $surchageEligibility->student_id;

                // Check if the user has already been processed
                if (!in_array($userId, $processedUsers)) {
                    $this->createSurchargeRecord($surchageEligibility, $count, $dueDate, $invoice_number);
                    // Mark the user as processed
                    $processedUsers[] = $userId;
                    // Create monthly payment record
                    $this->createMonthlyPaymentRecord($monthlyPaymentEligibleList, $dueDate, $invoice_number, 1);
                }
            }
        }
        
    }
}


    private function getSurchageEligibilitys($monthlyPaymentEligibleList, $dueDateThreshold)
    {
        $currentDate = Carbon::now();
        $dueDate4months = $currentDate->copy()->subMonths(4);
        return AccountPayable::where('admission_no', $monthlyPaymentEligibleList->sd_admission_no)
            ->where('eligibility', 1)
            ->where('status', 0)
            ->where('type', 'monthly')
            ->whereBetween('due_date', [$dueDate4months->format('Y-m-d'), Carbon::now()->format('Y-m-d')])
            ->get();     
    }

    private function createSurchargeRecord($surchageEligibility, $count, $dueDate, $invoice_number)
    {
         $surchargePercentages = [
            3 => 30,  // 10%, 20%, 30% for count == 3
            2 => 20,      // 10%, 20% for count == 2
            1 => 10,          // 10% for count == 1
            // Add more count => percentage mappings as needed
        ];
        $surchargeAmount = ($surchageEligibility->amount * $surchargePercentages[$count]) / 100;
        //Create surcharge record
        AccountPayable::create([
            'invoice_number' => $invoice_number,
            'admission_no' => $surchageEligibility->sd_admission_no,
            'amount' => $surchargeAmount,
            'type' => 'surcharge',
            'eligibility' => 0,
            'due_date' => $dueDate,
            'status' => 0,
        ]);

        // // Define surcharge percentages based on count values
        // $surchargePercentages = [
        //     3 => [10, 20, 30],  // 10%, 20%, 30% for count == 3
        //     2 => [10, 20],      // 10%, 20% for count == 2
        //     1 => [10],          // 10% for count == 1
        //     // Add more count => percentage mappings as needed
        // ];

        // // Default surcharge percentages if count is not found in the mappings
        // $defaultSurchargePercentages = [10];

        // // Determine the surcharge percentages based on count
        // $currentSurchargePercentages = $surchargePercentages[$count] ?? $defaultSurchargePercentages;

        // // Limit the loop to the minimum of the count and the number of specified percentages
        // $loopCount = min($count, count($currentSurchargePercentages));
        
        // // Loop through surcharge percentages and create individual records
        // for ($i = 0; $i < $loopCount; $i++) {
        //     // Calculate surcharge amount
        //     $surchargePercentage = $currentSurchargePercentages[$i];
        //     $surchargeAmount = ($surchageEligibility->amount * $surchargePercentage) / 100;

        //     //Create surcharge record
        //     AccountPayable::create([
        //         'invoice_number' => $invoice_number,
        //         'admission_no' => $surchageEligibility->sd_admission_no,
        //         'amount' => $surchargeAmount,
        //         'type' => 'surcharge',
        //         'eligibility' => 1,
        //         'due_date' => $dueDate,
        //         'status' => 0,
        //     ]);
        // }
    }


    private function createMonthlyPaymentRecord($monthlyPaymentEligibleList, $dueDate, $invoice_number, $eligibility)
    {
        AccountPayable::create([
            'invoice_number' => $invoice_number,
            'admission_no' => $monthlyPaymentEligibleList->sd_admission_no,
            'amount' => $monthlyPaymentEligibleList->yearGradeClass->monthly_fee,
            'type' => 'monthly',
            'eligibility' => $eligibility,
            'due_date' => $dueDate,
            'status' => 0,
        ]);
    }




    public function user_payments($id){
        $query = AccountPayable::where('invoice_number',$id)->get(); 
        return $query;
    }

    public function user_payment_update(array $data)
    {
        
        $current_amount = $data['payment_amount'];
        $current_user_invoice = Invoice::where('admission_no', $data['admission_id'])->whereIn('status', [0, 2])->get();
        if ( !$current_user_invoice) {
            throw new Exception("Sorry, No Available Invoice to pay", Response::HTTP_NOT_FOUND);
        }
        
        $studentPayment = new StudentPayment();
        $paymentId = Str::uuid();
        $invoiceNumbers = [];

        $newSdTotalDue = 0 ;
                
        // Create a new student payment record for all invoices
        $studentPayment->create([
            'payment_id' => $paymentId,
            'invoice_id' => json_encode($invoiceNumbers),
            'admission_no' => $current_user_invoice->first()->admission_no,
            'date' => now(),
            'due_date' => $current_user_invoice->first()->due_date,
            'total_due' => $data['payment_amount'],
            'status' => 1,
            'paid_from'=> $data['paid_from']
        ]);
       
        foreach ($current_user_invoice as $invoice) {
          
            if( $current_amount <= 0){break;}
              //get student total due and details of the studen extr payments
              $studentData =  StudentDetail::select('id','sd_total_due', 'sd_extra_pay', 'sd_payment_id' )
              ->where('sd_admission_no', $invoice->admission_no)
              ->first();
              $currentTotalOutstanding = $studentData->sd_total_due;
              $newSdTotalDue = 0 ;
            if($invoice->status == 2){//check invoie is partial paid invoice then update with due amount
               
                if($studentData != null && $studentData->sd_extra_pay){
                    //if total due value (extra payment value) equal to invoice amount,  invoive will automatically settle down
                    if($current_amount - ($invoice->total_due + $studentData->sd_total_due) == 0){
                    
                        $invoice->update(['status' => 1,
                                        'total_paid'=> $invoice->invoice_total,
                                        'total_due' => 0,
                                        'new_total_due' => 0, 
                                        'current_total_outstanding' =>$currentTotalOutstanding ]);
                        //update student payment table with adding this invoice number
                        $this->update_invoice_id($studentPayment->sd_payment_id,$invoice->invoice_number); 

                        // update invoice number when invoice automatically payied
                        AccountPayable::where('invoice_number', $invoice->invoice_number)->update(['status' => 1]);
                        $newSdTotalDue = 0;
                        //update student deatil page 
                        $studentData->sd_total_due = 0; // Set sd_total_due to 0
                        $studentData->sd_extra_pay = false; // Set sd_extra_pay to false
                        $studentData->sd_payment_id  = "";
                        $studentData->save(); // Save the changes

                        $current_amount = 0;

                    }else if($current_amount - ($invoice->total_due + $studentData->sd_total_due)  < 0 ){
                        //fully paid but  have some extra amount for next invoice
                        $invoice->update(['status' => 1,
                                            'total_paid'=> $invoice->invoice_total, 
                                            'total_due' => 0,
                                            'new_total_due' => (($invoice->total_due + $studentData->sd_total_due) - $current_amount), 
                                            'current_total_outstanding' =>$currentTotalOutstanding ]);

                        //update student payment table with adding this invoice number
                        $this->update_invoice_id($paymentId,$invoice->invoice_number); 

                        // update invoice number when invoice payied
                        AccountPayable::where('invoice_number', $invoice->invoice_number)->update(['status' => 1]);

                        //update student deatil page 
                        $newSdTotalDue = (($invoice->total_due + $studentData->sd_total_due) - $current_amount);
                        $studentData->sd_total_due = (($invoice->total_due + $studentData->sd_total_due) - $current_amount); // Set sd_total_due to 0
                        $studentData->sd_extra_pay = true; // Set sd_extra_pay to false
                        $studentData->save(); // Save the changes

                        $current_amount = ($current_amount - ($invoice->total_due + $studentData->sd_total_due));
                        
                    }else{
                        //patiolly paid records

                        $invoice->update(['status' => 2,
                                        'total_paid'=> ($invoice->total_paid + ($studentData->sd_total_due * -1)  +  $current_amount ), 
                                        'total_due' => ($invoice->invoice_total - ($invoice->total_paid + ($studentData->sd_total_due * -1)  +  $current_amount )),
                                        'new_total_due' => ($invoice->invoice_total - ($invoice->total_paid + ($studentData->sd_total_due * -1)  +  $current_amount )), 
                                        'current_total_outstanding' =>$currentTotalOutstanding
                                     ]);
                       
                        //update student payment table with adding this invoice number
                        $this->update_invoice_id($studentPayment->payment_id,$invoice->invoice_number); 
                        // update invoice number when invoice payied
                        AccountPayable::where('invoice_number', $invoice->invoice_number)->update(['status' => 2]);
                         //update student deatil page 
                        $newSdTotalDue = ($invoice->invoice_total - ($invoice->total_paid + ($studentData->sd_total_due * -1)  +  $current_amount ));
                        $studentData->sd_total_due = ($invoice->invoice_total - ($invoice->total_paid + ($studentData->sd_total_due * -1)  +  $current_amount )); // Set sd_total_due to 0
                        $studentData->sd_extra_pay = false; // Set sd_extra_pay to false
                        $studentData->sd_payment_id  = "";
                        $studentData->save(); // Save the changes
                    }
                }else{
                    if ($current_amount >= $invoice->total_due) {
                        // If there's enough amount, deduct the invoice total from the current amount
                        $current_amount -= $invoice->total_paid;
                        $invoice->update(['status' =>1,
                                            'total_paid'=> $invoice->invoice_total, 
                                            'total_due' => 0,
                                            'new_total_due' => ($current_amount == $invoice->total_due) ? 0 :  ($invoice->total_due - $current_amount), 
                                            'current_total_outstanding' =>$currentTotalOutstanding
                                        ]);
                        
                         //update student payment table with adding this invoice number
                         $this->update_invoice_id($paymentId,$invoice->invoice_number); 

                          // update invoice number when invoice payied
                        AccountPayable::where('invoice_number', $invoice->invoice_number)->update(['status' => 1]);

                         //update student deatil page 
                        $newSdTotalDue = ($invoice->total_due - $current_amount);
                       
                        $studentData->sd_total_due = ($current_amount == $invoice->total_due) ? 0 :  ($invoice->total_due - $current_amount);  // Set sd_total_due to 0
                        $studentData->sd_extra_pay = ($current_amount == $invoice->total_due) ? false : true; // Set sd_extra_pay to false
                        $studentData->sd_payment_id  = ($current_amount == $invoice->total_due) ? "" : $paymentId;
                        $studentData->save(); // Save the changes
                    } else {

                        $invoice->update(['status' => 2,
                            'total_paid'=> ($invoice->total_paid  +  $current_amount ), 
                            'total_due' => ($invoice->invoice_total - ($invoice->total_paid  +  $current_amount )),
                            'new_total_due' => ($invoice->invoice_total - ($invoice->total_paid  +  $current_amount )), 
                            'current_total_outstanding' =>$currentTotalOutstanding
                        ]);
       
                        //update student payment table with adding this invoice number
                        $this->update_invoice_id($paymentId,$invoice->invoice_number); 

                         // update invoice number when invoice payied
                         AccountPayable::where('invoice_number', $invoice->invoice_number)->update(['status' => 2]);
                         $newSdTotalDue = ($invoice->invoice_total - ($invoice->total_paid  +  $current_amount ));
                        //update student deatil page 
                        $studentData->sd_total_due = ($invoice->invoice_total - ($invoice->total_paid  +  $current_amount )); // Set sd_total_due to 0
                        $studentData->sd_extra_pay = false; // Set sd_extra_pay to false
                        $studentData->sd_payment_id  = "";
                        $studentData->save(); // Save the changes
                    }
                  
               }
            }
            else{
                if ($current_amount >= $invoice->invoice_total) {
                    // If there's enough amount, deduct the invoice total from the current amount
                    $current_amount -= $invoice->invoice_total;
                    $invoice->update(['status' =>1,
                        'total_paid'=> $invoice->invoice_total, 
                        'total_due' => ($current_amount == $invoice->invoice_total) ? 0 :  ($invoice->invoice_total - $current_amount),
                        'new_total_due' => ($current_amount == $invoice->invoice_total) ? 0 :  ($invoice->invoice_total - $current_amount),
                        'current_total_outstanding' =>$currentTotalOutstanding
                
                ]);
                    
                     //update student payment table with adding this invoice number
                     $this->update_invoice_id($paymentId,$invoice->invoice_number); 

                      // update invoice number when invoice payied
                    AccountPayable::where('invoice_number', $invoice->invoice_number)->update(['status' => 1]);
                    $newSdTotalDue = ($current_amount == $invoice->invoice_total) ? 0 :  ($invoice->invoice_total - $current_amount); 
                     //update student deatil page 
                    $studentData->sd_total_due = ($current_amount == $invoice->invoice_total) ? 0 :  ($invoice->invoice_total - $current_amount);  // Set sd_total_due to 0
                    $studentData->sd_extra_pay = ($current_amount == $invoice->invoice_total) ? false : true; // Set sd_extra_pay to false
                    $studentData->sd_payment_id  = ($current_amount == $invoice->invoice_total) ? "" : $paymentId;
                    $studentData->save(); // Save the changes
                } else {

                    $invoice->update(['status' => 2,
                        'total_paid'=> ($current_amount ), 
                        'total_due' => ($invoice->invoice_total - $current_amount ),
                        'new_total_due' => ($invoice->invoice_total - $current_amount ),
                        'current_total_outstanding' =>$currentTotalOutstanding
                    ]);
   
                    //update student payment table with adding this invoice number
                    $this->update_invoice_id($paymentId,$invoice->invoice_number); 

                     // update invoice number when invoice payied
                     AccountPayable::where('invoice_number', $invoice->invoice_number)->update(['status' => 2]);
                     
                    //update student deatil page 
                    $studentData->sd_total_due = ($invoice->invoice_total - $current_amount ); // Set sd_total_due to 0
                    $studentData->sd_extra_pay = false; // Set sd_extra_pay to false
                    $studentData->sd_payment_id  = "";
                    $studentData->save(); // Save the changes
                }
            }
            // // Check if there's enough amount to cover the current invoice
            // if ($current_amount >= $invoice->invoice_total) {
            //     // If there's enough amount, deduct the invoice total from the current amount
            //     $current_amount -= $invoice->invoice_total;
              
                
            //     $outstanding_balance = 0; // No balance remaining
            //     $status = 1;
            //     // Update the status to 1 (fully paid)
            //     $invoice->update(['status' => 1]);
            // } else {
            //     // If the current amount is not enough to cover the entire invoice
            //     // Store the partial payment in the total_due column 
            //     // Update the status to 2 (partial payment)
                
                
            //     $outstanding_balance = $invoice->invoice_total - $current_amount;
                
            //      // No amount remaining
            //      $status = 2;
            //     // Update the status to 2 (partial payment)
            //      $invoice->update(['status' => 2, 'total_paid' => $current_amount]);
            // }

            // $invoiceNumbers[] = $invoice->invoice_number;
            
            // $this->updateAccountPaymentTable($data['selectedInvoices'][0]['admission_id'],$current_amount,$status,$data['selectedInvoices'][0]['payment_amount']);
        }
        return $studentPayment;
       
    }
    
    private function updateAccountPaymentTable($admission_id,$current_amount,$status,$current_payment){


            DB::beginTransaction();

        try {   
                $current_invoice_related_data = AccountPayable::where('admission_no', $admission_id )->where('status',0)->get();
                $invoiceData = $current_invoice_related_data->map(function ($invoice) {
                return [
                    'id' => $invoice->id,
                    'amount' => $invoice->amount,
                    'type' => $invoice->type,
                ];
            })->toArray();
             foreach ($invoiceData as $invoice) {
                    if ($invoice['amount'] <= $current_payment) {
                        // Update the record to status 1
                        AccountPayable::where('id', $invoice['id'])->update(['status' => 1]);
                    } else {
                        // Update the record partially to status 2
                        AccountPayable::where('id', $invoice['id'])->update(['status' => 2]);

                        // You can also store the remaining amount in the database or use it for further processing
                        $remaining_amount = $invoice['amount'] - $current_payment;
                        // ... do something with $remaining_amount if needed
                    }
                }
             
             DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            // Handle the exception as needed
            throw $e;
        }
        
    }

    public function prepareForDB(array $data, ?StudentPayment $master_class = null): array
    {
        return [
            'student_id' => $data['organization_id'],
            'due_amount' => $data['class_name'],
            'due_amount' => $data['class_name'],

        ];
    }

    public function current_user_pay(array $data)
    {
            $admissionId = $data['admission_id'];
            $date = $data['date'];
            // Assuming you have a relationship between users and invoices, adjust this based on your actual relationship
            $invoices = Invoice::where('admission_no', $admissionId)
            ->whereIn('status', [0, 2]) // Assuming 0 means unpaid
            // ->where('due_date', '<', $date)
            ->get();
            // if ( !$invoices) {
            //     throw new Exception("Sorry, No Available Invoice to pay", Response::HTTP_NOT_FOUND);
            // }
        return $invoices;
    }
    public function all_user_pay(array $data) {

        $studentPaymentquery = StudentPayment::select("*");
        if(array_key_exists("admission_id", $data) &&  $data['admission_id'] != null ){
            $studentPaymentquery->where('admission_no', $data['admission_id']);
        }
        if(array_key_exists("sd_year_grade_class_id", $data) &&  $data['sd_year_grade_class_id'] != null ){
            $studentDetails = StudentDetail::where('sd_year_grade_class_id', $data['sd_year_grade_class_id'])
            ->select('sd_admission_no') 
            ->get();

            $admissionNumbers = $studentDetails->pluck('sd_admission_no')->toArray();
            $studentPaymentquery->whereIn('admission_no', $admissionNumbers);
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
        $studentDetails = $studentPaymentquery->get();
        if (empty( $studentDetails)) {
            throw new Exception("Student details not found", Response::HTTP_NOT_FOUND);
        }
        // $formattedData = [];

        // foreach ($studentDetails as $studentDetail) {
        //     $formattedStudent = $studentDetail->toArray();

        //     // Separate invoice_id array and retrieve related data
        //     $invoiceIds = json_decode($formattedStudent['student_payment'][0]['invoice_id'], true);

        //     $invoicesQuery = Invoice::whereIn('invoice_number', $invoiceIds);
        //     if(array_key_exists("date", $data)){
        //         $invoicesQuery->where('due_date','<',$data['date'])->get();
        //     }
        //     $invoices = $invoicesQuery->get();

        //     $formattedInvoices = [];
        //     foreach ($invoices as $invoice) {
        //         $formattedInvoice = $invoice->toArray();

        //         $invoice_items = AccountPayable::where('invoice_number', $invoice->invoice_number)->get();

        //         $formattedInvoice['invoice_items'] = $invoice_items->toArray();

        //         $formattedInvoices[] = $formattedInvoice;
        //     }

        //     // Move invoices into the student_payment array
        //     $formattedStudent['student_payment'][0]['invoices'] = $formattedInvoices;

        //     // Remove the original invoice_id field
        //     unset($formattedStudent['student_payment'][0]['invoice_id']);

        //     $formattedData[] = $formattedStudent;
        // }

        return  $studentDetails;
    }

    public static function get_payment_detail(array $data) {
        $studentPaymentquery = StudentPayment::select("student_payments.*", 
                                DB::raw('student_details.sd_name_with_initials'),
                                DB::raw('student_details.sd_address_line1'),
                                DB::raw('student_details.sd_address_line2'),
                                DB::raw('student_details.sd_address_city'),
                                DB::raw('student_details.sd_telephone_mobile'),
                                DB::raw('student_details.sd_email_address'));
        if(array_key_exists("payment_id", $data) &&  $data['payment_id'] != null ){
            $studentPaymentquery->where('payment_id', $data['payment_id']);
        }else {
            throw new Exception("Pyament Id is required", Response::HTTP_NOT_FOUND);
        }

        $studentPaymentquery->join('student_details', 'student_details.sd_admission_no', '=', 'student_payments.admission_no');
        $studentPaymentDetail = $studentPaymentquery->first();

        if (empty( $studentPaymentDetail)) {
            throw new Exception("Payment details not found", Response::HTTP_NOT_FOUND);
        }


        $InvoiceNumbers = json_decode($studentPaymentDetail->invoice_id, true) ?? [];

        $invoiceData = Invoice::select('invoices.*')
                            ->whereIn('invoices.invoice_number', $InvoiceNumbers)->get();


        foreach ($invoiceData as $key => $invoice) {
            $invoiceData[$key]->accountPayables = AccountPayable::where('invoice_number',$invoice->invoice_number)
            ->get();
        }

        $studentPaymentDetail->invoiceDetails = $invoiceData;

        return  $studentPaymentDetail;

        
    }

    public function invoice_generate()
        {
            $currentDate = Carbon::now();
            $dueDate = $currentDate->copy()->addMonth();

            $uniqueInvoices = AccountPayable::whereIn('status', [0, 2])
                ->select('invoice_number', DB::raw('SUM(amount) as amount'),'admission_no')
                ->groupBy('invoice_number','admission_no')
                ->get();

            foreach ($uniqueInvoices as $uniqueInvoice) {

                //get student total due and details of the studen extr payments
               $studentData =  StudentDetail::select('id','sd_total_due', 'sd_extra_pay', 'sd_payment_id' )
                ->where('sd_admission_no', $uniqueInvoice->admission_no)
                ->first();
                // $studentPayment = StudentPayment::where('payment_id', $studentData->sd_payment_id)->first();
                $exsistingInvoiceData =  Invoice::where('invoice_number' , $uniqueInvoice->invoice_number)->first();

                $totalPaid = 0;
                $totalDue = 0;
                $status = 0;
                $newSdTotalDue = 0 ;
                $currentTotalOutstanding = $studentData->sd_total_due;

                if($exsistingInvoiceData && $exsistingInvoiceData->status == 2){ // if status is patial then total pain logic will defernt

                    if($studentData != null && $studentData->sd_extra_pay){
                        //if total due value (extra payment value) equal to invoice amount,  invoive will automatically settle down
                            if(($exsistingInvoiceData->total_due + $studentData->sd_total_due) == 0){
    
                                $totalPaid = $uniqueInvoice->amount;
                                $totalDue = 0;
                                $status = 1;
                                $newSdTotalDue =  ($studentData->sd_total_due +  $exsistingInvoiceData->total_due);
    
                                //update student payment table with adding this invoice number
                                $this->update_invoice_id($studentData->sd_payment_id,$uniqueInvoice->invoice_number); 
    
                                // update invoice number when invoice automatically payied
                                AccountPayable::where('invoice_number', $uniqueInvoice->invoice_number)->update(['status' => 1]);
    
                                //update student deatil page 
                                $studentData->sd_total_due = 0; // Set sd_total_due to 0
                                $studentData->sd_extra_pay = false; // Set sd_extra_pay to false
                                $studentData->sd_payment_id  = "";
                                $studentData->save(); // Save the changes
    
                                
    
                            }else if(($exsistingInvoiceData->total_due + $studentData->sd_total_due)  < 0 ){
                                //fully paid but  have some extra amount for next invoice
                                $totalPaid = $uniqueInvoice->amount;
                                $totalDue = 0;
                                $status = 1;
                                $newSdTotalDue =  ($studentData->sd_total_due +  $exsistingInvoiceData->total_due);
                                //update student payment table with adding this invoice number
                                $this->update_invoice_id($studentData->sd_payment_id,$uniqueInvoice->invoice_number); 
    
                                // update invoice number when invoice automatically payied
                                AccountPayable::where('invoice_number', $uniqueInvoice->invoice_number)->update(['status' => 1]);
    
                                //update student deatil page 
                                $studentData->sd_total_due = $exsistingInvoiceData->total_due + $studentData->sd_total_due; // Set sd_total_due to 0
                                $studentData->sd_extra_pay = true; // Set sd_extra_pay to false
                                $studentData->save(); // Save the changes
                                  
                                //update student payment table with adding this invoice number
                            }else{
                                //patiolly paid records
                                $totalPaid = ($exsistingInvoiceData->total_paid + ($studentData->sd_total_due * -1 ));
                                $totalDue = (($uniqueInvoice->amount + $studentData->sd_total_due) - $exsistingInvoiceData->total_paid);
                                $status = 2;
                                
                                $newSdTotalDue =  ($studentData->sd_total_due + $exsistingInvoiceData->total_due);
                                //update student payment table with adding this invoice number
                                $this->update_invoice_id($studentData->sd_payment_id,$uniqueInvoice->invoice_number); 
    
                                 //update student deatil page 
                                $studentData->sd_total_due = 0; // Set sd_total_due to 0
                                $studentData->sd_extra_pay = false; // Set sd_extra_pay to false
                                $studentData->sd_payment_id  = "";
                                $studentData->save(); // Save the changes
                            }
                    }else{
                      
                        
                         $totalPaid = $exsistingInvoiceData->total_paid;
                         $totalDue = $exsistingInvoiceData->total_due;
                         $status = $exsistingInvoiceData->status;
                         
                         //update student deatil page 
                         $studentData->sd_total_due =  ($studentData->sd_total_due); //add total due with current invoice ammount
                         $studentData->sd_extra_pay = false; // Set sd_extra_pay to false
                         $studentData->save(); // Save the changes
                         $newSdTotalDue =   $exsistingInvoiceData->new_total_due;
                         $currentTotalOutstanding =  $exsistingInvoiceData->current_total_outstanding;
                    }
                }else if($studentData != null && $studentData->sd_extra_pay){
                    //if total due value (extra payment value) equal to invoice amount,  invoive will automatically settle down
                        if(($uniqueInvoice->amount + $studentData->sd_total_due) == 0){

                            $totalPaid = $uniqueInvoice->amount;
                            $totalDue = 0;
                            $status = 1;
                            $newSdTotalDue =  ($studentData->sd_total_due +  $uniqueInvoice->amount);

                            //update student payment table with adding this invoice number
                            $this->update_invoice_id($studentData->sd_payment_id,$uniqueInvoice->invoice_number); 

                            // update invoice number when invoice automatically payied
                            AccountPayable::where('invoice_number', $uniqueInvoice->invoice_number)->update(['status' => 1]);

                            //update student deatil page 
                            $studentData->sd_total_due = 0; // Set sd_total_due to 0
                            $studentData->sd_extra_pay = false; // Set sd_extra_pay to false
                            $studentData->sd_payment_id  = "";
                            $studentData->save(); // Save the changes

                            

                        }else if(($uniqueInvoice->amount + $studentData->sd_total_due)  < 0 ){
                            //fully paid but  have some extra amount for next invoice
                            $totalPaid = $uniqueInvoice->amount;
                            $totalDue = 0;
                            $status = 1;
                            $newSdTotalDue =  ($studentData->sd_total_due +  $uniqueInvoice->amount);
                            //update student payment table with adding this invoice number
                            $this->update_invoice_id($studentData->sd_payment_id,$uniqueInvoice->invoice_number); 

                            // update invoice number when invoice automatically payied
                            AccountPayable::where('invoice_number', $uniqueInvoice->invoice_number)->update(['status' => 1]);

                            //update student deatil page 
                            $studentData->sd_total_due =$uniqueInvoice->amount + $studentData->sd_total_due; // Set sd_total_due to 0
                            $studentData->sd_extra_pay = true; // Set sd_extra_pay to false
                            $studentData->save(); // Save the changes
                              
                            //update student payment table with adding this invoice number
                        }else{
                            //patiolly paid records
                            $totalPaid = ($studentData->sd_total_due * -1 );
                            $totalDue = $uniqueInvoice->amount + $studentData->sd_total_due;
                            $status = 2;
                            
                            $newSdTotalDue =  ($studentData->sd_total_due + $uniqueInvoice->amount);
                            //update student payment table with adding this invoice number
                            $this->update_invoice_id($studentData->sd_payment_id,$uniqueInvoice->invoice_number); 

                             //update student deatil page 
                            $studentData->sd_total_due = 0; // Set sd_total_due to 0
                            $studentData->sd_extra_pay = false; // Set sd_extra_pay to false
                            $studentData->sd_payment_id  = "";
                            $studentData->save(); // Save the changes
                        }
                }else{
                     //update student deatil page 
                     $studentData->sd_total_due =  ($studentData->sd_total_due + $uniqueInvoice->amount); //add total due with current invoice ammount
                     $studentData->sd_extra_pay = false; // Set sd_extra_pay to false
                     $studentData->save(); // Save the changes
                     $newSdTotalDue =  ($studentData->sd_total_due +  $uniqueInvoice->amount);
                }
              
                Invoice::updateOrCreate(
                    ['invoice_number' => $uniqueInvoice->invoice_number],
                    [
                        'admission_no' => $uniqueInvoice->admission_no,
                        'due_date' => $dueDate,
                        'invoice_total' => $uniqueInvoice->amount,
                        'total_paid' =>$totalPaid,
                        'total_due' => $totalDue,
                        'status' => $status,
                        'new_total_due' => $newSdTotalDue, 
                        'current_total_outstanding' =>$currentTotalOutstanding,
                    ]
                );

            }

            return $uniqueInvoices;
    }

    
    public function update_invoice_id($paymentId,$invoice_number ){
        $studentPayment = StudentPayment::where('payment_id', $paymentId)->first();

        

        $invoiceIds = json_decode($studentPayment->invoice_id, true) ?? []; // Get existing invoice IDs as array
        $newInvoiceId = $invoice_number;
        
        // Add the new invoice ID if it's not already in the array
        if (!in_array($newInvoiceId, $invoiceIds)) {
            $invoiceIds[] = $newInvoiceId;
            $studentPayment->invoice_id = json_encode($invoiceIds); // Update invoice_id with the new array
            $studentPayment->save(); // Save the changes
        }
    }

    public static function get_account_payables(Request $request) {

        $accountPaybales = AccountPayable::select('*');
        if($request->filled('admission_id')){
            $accountPaybales->where('admission_no',$request->admission_id);
        }
        if($request->filled('sd_year_grade_class_id')){
            $studentDetails = StudentDetail::where('sd_year_grade_class_id', $request->sd_year_grade_class_id)
            ->select('sd_admission_no') 
            ->get();

            $admissionNumbers = $studentDetails->pluck('sd_admission_no')->toArray();
            $accountPaybales->whereIn('admission_no', $admissionNumbers);
        
        }

        if($request->filled('from_date') && $request->filled('to_date')){
            try {
                $startDate = Carbon::parse($request->from_date); 
                $endDate = Carbon::parse($request->to_date);

                $accountPaybales->whereBetween('created_at', [$startDate, $endDate]);
            } catch (\Throwable $th) {
             
            }
    
        }else if($request->filled('from_date') ){
            try {
                $startDate = Carbon::parse($request->from_date); 
                $accountPaybales->where('created_at','<=', $startDate);
                // $accountPaybales->where('created_at','>=', );
            } catch (\Throwable $th) {
               
            }
        }else if($request->filled('to_date') ){
            try {
                $endDate = Carbon::parse($request->to_date);
                $accountPaybales->where('created_at','>=', $endDate);
            } catch (\Throwable $th) {
               
            }
        }
        // else{
        //     $currentDate = Carbon::now();

        //     // First day of the current month
        //     $firstDayOfMonth = $currentDate->startOfMonth();
            
        //     // Last day of the current month
        //     $lastDayOfMonth = $currentDate->endOfMonth();
            
        //     $accountPaybales->whereBetween('created_at', [$firstDayOfMonth, $lastDayOfMonth]);
             
        // }
        $accountPaybalesData = $accountPaybales->get();
        
        if (empty( $accountPaybalesData)) {
            throw new Exception("Account Paybles does not exist.", Response::HTTP_NOT_FOUND);
        }
        return  $accountPaybalesData;

        
    }

    public static function get_all_invoices(Request $request) {

        $invoiceData = Invoice::select(
            'invoices.*',
            DB::raw('student_details.sd_name_with_initials'),
            DB::raw('student_details.sd_address_line1'),
            DB::raw('student_details.sd_address_line2'),
            DB::raw('student_details.sd_address_city'),
            DB::raw('student_details.sd_telephone_mobile'),
            DB::raw('student_details.sd_email_address')
        );
        if($request->filled('admission_id')){
            $invoiceData->where('invoices.admission_no',$request->admission_id);
        }
        if($request->filled('sd_year_grade_class_id')){
           
            $studentDetails = StudentDetail::where('sd_year_grade_class_id', $request->sd_year_grade_class_id)
            ->select('sd_admission_no') 
            ->get();
            
            $admissionNumbers = $studentDetails->pluck('sd_admission_no')->toArray();
            $invoiceData->whereIn('invoices.admission_no', $admissionNumbers);
            
        }

        if($request->filled('from_date') && $request->filled('to_date')){
            try {
                $startDate = Carbon::parse($request->from_date); 
                $endDate = Carbon::parse($request->to_date);

                $invoiceData->whereBetween('invoices.created_at', [$startDate, $endDate]);
            } catch (\Throwable $th) {
             
            }
    
        }else if($request->filled('from_date') ){
            try {
                $startDate = Carbon::parse($request->from_date); 
                $invoiceData->where('invoices.created_at','<=', $startDate);
                
            } catch (\Throwable $th) {
               
            }
        }else if($request->filled('to_date') ){
            try {
                $endDate = Carbon::parse($request->to_date);
                $invoiceData->where('invoices.created_at','>=', $endDate);
            } catch (\Throwable $th) {
               
            }
        }
        // else{
        //     $currentDate = Carbon::now();

        //     // First day of the current month
        //     $firstDayOfMonth = $currentDate->startOfMonth();
            
        //     // Last day of the current month
        //     $lastDayOfMonth = $currentDate->endOfMonth();
            
        //     $invoiceData->whereBetween('created_at', [$firstDayOfMonth, $lastDayOfMonth]);
             
        // }
        $invoiceData->join('student_details', 'student_details.sd_admission_no', '=', 'invoices.admission_no');
        $invoiceDataData = $invoiceData->get();
        
        if (empty( $invoiceDataData)) {
            throw new Exception("Invoice does not exist.", Response::HTTP_NOT_FOUND);
        }

        // foreach ($invoiceDataData as $key => $invoice) {
        //     $invoiceDataData[$key]->accountPayables = AccountPayable::where('invoice_number',$invoice->invoice_number)
        //     ->get();
        // }


        return  $invoiceDataData;

        
    }

    public static function get_invoice_detail(Request $request) {

        $invoiceData = Invoice::select(
            'invoices.*',
            DB::raw('student_details.sd_name_with_initials'),
            DB::raw('student_details.sd_address_line1'),
            DB::raw('student_details.sd_address_line2'),
            DB::raw('student_details.sd_address_city'),
            DB::raw('student_details.sd_telephone_mobile'),
            DB::raw('student_details.sd_email_address')
           
        );
        if($request->filled('invoice_number')){
            $invoiceData->where('invoices.invoice_number',$request->invoice_number);
        }else{
            throw new Exception("Invoice Number is Required.", Response::HTTP_NOT_FOUND);
        }
       
        $invoiceData->join('student_details', 'student_details.sd_admission_no', '=', 'invoices.admission_no');
        $invoiceDataData = $invoiceData->get();
        
        if (empty( $invoiceDataData)) {
            throw new Exception("Invoice does not exist.", Response::HTTP_NOT_FOUND);
        }

        foreach ($invoiceDataData as $key => $invoice) {
            $invoiceDataData[$key]->accountPayables = AccountPayable::where('invoice_number',$invoice->invoice_number)
            ->get();
        }


        return  $invoiceDataData;

        
    }




}


