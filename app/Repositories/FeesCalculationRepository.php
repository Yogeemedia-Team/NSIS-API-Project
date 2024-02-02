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
        return StudentDetail::where('sd_academic_status', 1)
            ->with('yearGradeClass')
            ->get();
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
        
        if($count == 0){
            $this->createMonthlyPaymentRecord($monthlyPaymentEligibleList, $dueDate, $invoice_number);
        }else{
            foreach ($surchageEligibilitys as $surchageEligibility) {
                $userId = $surchageEligibility->student_id;

                // Check if the user has already been processed
                if (!in_array($userId, $processedUsers)) {
                    $this->createSurchargeRecord($surchageEligibility, $count, $dueDate, $invoice_number);
                    // Mark the user as processed
                    $processedUsers[] = $userId;
                    // Create monthly payment record
                    $this->createMonthlyPaymentRecord($monthlyPaymentEligibleList, $dueDate, $invoice_number);
                }
            }
        }
        
    }
}


    private function getSurchageEligibilitys($monthlyPaymentEligibleList, $dueDateThreshold)
    {
        
        return AccountPayable::where('admission_no', $monthlyPaymentEligibleList->sd_admission_no)
            ->where('eligibility', 1)
            ->where('status', 0)
            ->where('type', 'monthly')
            ->whereBetween('due_date', [$dueDateThreshold->format('Y-m-d'), Carbon::now()->format('Y-m-d')])
            ->get();     
    }

    private function createSurchargeRecord($surchageEligibility, $count, $dueDate, $invoice_number)
    {
        // Define surcharge percentages based on count values
        $surchargePercentages = [
            3 => [10, 20, 30],  // 10%, 20%, 30% for count == 3
            2 => [10, 20],      // 10%, 20% for count == 2
            1 => [10],          // 10% for count == 1
            // Add more count => percentage mappings as needed
        ];

        // Default surcharge percentages if count is not found in the mappings
        $defaultSurchargePercentages = [10];

        // Determine the surcharge percentages based on count
        $currentSurchargePercentages = $surchargePercentages[$count] ?? $defaultSurchargePercentages;

        // Limit the loop to the minimum of the count and the number of specified percentages
        $loopCount = min($count, count($currentSurchargePercentages));
        
        // Loop through surcharge percentages and create individual records
        for ($i = 0; $i < $loopCount; $i++) {
            // Calculate surcharge amount
            $surchargePercentage = $currentSurchargePercentages[$i];
            $surchargeAmount = ($surchageEligibility->amount * $surchargePercentage) / 100;

            //Create surcharge record
            AccountPayable::create([
                'invoice_number' => $invoice_number,
                'admission_no' => $surchageEligibility->sd_admission_no,
                'amount' => $surchargeAmount,
                'type' => 'surcharge',
                'eligibility' => 1,
                'due_date' => $dueDate,
                'status' => 0,
            ]);
        }
    }


    private function createMonthlyPaymentRecord($monthlyPaymentEligibleList, $dueDate, $invoice_number)
    {
        AccountPayable::create([
            'invoice_number' => $invoice_number,
            'admission_no' => $monthlyPaymentEligibleList->sd_admission_no,
            'amount' => $monthlyPaymentEligibleList->yearGradeClass->monthly_fee,
            'type' => 'monthly',
            'eligibility' => 1,
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
        
        $current_amount = $data['selectedInvoices'][0]['payment_amount'];
        $current_user_invoice = Invoice::where('admission_no', $data['selectedInvoices'][0]['admission_id'])->whereIn('status', [0, 2])->get();

        $studentPayment = new StudentPayment();
        $paymentId = Str::uuid();
        $invoiceNumbers = [];

        foreach ($current_user_invoice as $invoice) {
            // Check if there's enough amount to cover the current invoice
            if ($current_amount >= $invoice->invoice_total) {
                // If there's enough amount, deduct the invoice total from the current amount
                $current_amount -= $invoice->invoice_total;
              
                
                $outstanding_balance = 0; // No balance remaining
                $status = 1;
                // Update the status to 1 (fully paid)
                $invoice->update(['status' => 1]);
            } else {
                // If the current amount is not enough to cover the entire invoice
                // Store the partial payment in the total_due column 
                // Update the status to 2 (partial payment)
                
                
                $outstanding_balance = $invoice->invoice_total - $current_amount;
                
                 // No amount remaining
                 $status = 2;
                // Update the status to 2 (partial payment)
                 $invoice->update(['status' => 2, 'total_paid' => $current_amount]);
            }

            $invoiceNumbers[] = $invoice->invoice_number;
            
            $this->updateAccountPaymentTable($data['selectedInvoices'][0]['admission_id'],$current_amount,$status,$data['selectedInvoices'][0]['payment_amount']);
        }

        $invoiceNumbersString = json_encode($invoiceNumbers);

       // Create a new student payment record for all invoices
        $studentPayment->create([
            'payment_id' => $paymentId,
            'invoice_id' => $invoiceNumbersString,
            'admission_no' => $current_user_invoice->first()->admission_no,
            'date' => now(),
            'due_date' => $current_user_invoice->first()->due_date,
            'total_due' => $data['selectedInvoices'][0]['payment_amount'],
            'status' => 1,
        ]);
                            
             
        



            

        
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
        ->where('due_date', '<', $date)
        ->get();

    return $invoices;
}
public function all_user_pay(array $data) {
    $admissionId = $data['admission_id'];
    $class = $data['sd_year_grade_class_id'];
    $date = $data['date'];
    // Assuming you have a direct relationship between StudentDetail and Invoice
    $studentDetails = StudentDetail::with('StudentPayment')
        ->where('sd_admission_no', $admissionId)
        ->where('sd_year_grade_class_id', $class)
        ->get();

    $formattedData = [];

    foreach ($studentDetails as $studentDetail) {
        $formattedStudent = $studentDetail->toArray();

        // Separate invoice_id array and retrieve related data
        $invoiceIds = json_decode($formattedStudent['student_payment'][0]['invoice_id'], true);

        $invoices = Invoice::whereIn('invoice_number', $invoiceIds)->where('due_date','<',$date)->get();

        $formattedInvoices = [];
        foreach ($invoices as $invoice) {
            $formattedInvoice = $invoice->toArray();

            $invoice_items = AccountPayable::where('invoice_number', $invoice->invoice_number)->get();

            $formattedInvoice['invoice_items'] = $invoice_items->toArray();

            $formattedInvoices[] = $formattedInvoice;
        }

        // Move invoices into the student_payment array
        $formattedStudent['student_payment'][0]['invoices'] = $formattedInvoices;

        // Remove the original invoice_id field
        unset($formattedStudent['student_payment'][0]['invoice_id']);

        $formattedData[] = $formattedStudent;
    }

    return  $formattedData;
}



    public function invoice_generate()
        {
            $currentDate = Carbon::now();
            $dueDate = $currentDate->copy()->addMonth();

            $uniqueInvoices = AccountPayable::where('status', 0)
                ->select('invoice_number', DB::raw('SUM(amount) as amount'),'admission_no')
                ->groupBy('invoice_number','admission_no')
                ->get();

            foreach ($uniqueInvoices as $uniqueInvoice) {
                Invoice::updateOrCreate(
                    ['invoice_number' => $uniqueInvoice->invoice_number],
                    [
                        'admission_no' => $uniqueInvoice->admission_no,
                        'due_date' => $dueDate,
                        'invoice_total' => $uniqueInvoice->amount,
                        'total_paid' => 0,
                        'total_due' => 0,
                        'status' => 0,
                    ]
                );
            }

            return $uniqueInvoices;
    }







}


