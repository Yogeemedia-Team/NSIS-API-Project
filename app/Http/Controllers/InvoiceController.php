<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;
use App\Repositories\FeesCalculationRepository;
use Exception;
use App\Traits\ResponseTrait;
use Illuminate\Http\JsonResponse;

use Illuminate\Support\Facades\Validator;
 
class InvoiceController extends Controller
{

    use ResponseTrait;
    public function user_wise_invoices(Request $request){

        $query = Invoice::where('admission_no', $request->admission_id)->get();
         
        return $query;
    }

    public function account_payables(Request $request): JsonResponse{
        try {
            return $this->responseSuccess(FeesCalculationRepository::get_account_payables($request), 'Account Payables List Successfull');
        } catch (Exception $exception) {
            return $this->responseError([], $exception->getMessage(), $exception->getCode());
        }
    }

    public function invoice_list(Request $request): JsonResponse{
        try {
            return $this->responseSuccess(FeesCalculationRepository::get_all_invoices($request), 'Invoice List Successfull');
        } catch (Exception $exception) {
            return $this->responseError([], $exception->getMessage(), $exception->getCode());
        }
    }

    
    public function invoice_details(Request $request): JsonResponse{
        try {
            return $this->responseSuccess(FeesCalculationRepository::get_invoice_detail($request), 'Invoice Detail Received Successfull');
        } catch (Exception $exception) {
            return $this->responseError([], $exception->getMessage(), $exception->getCode());
        }
    }

    public function revice_sercharge(Request $request){

        try {
            // Validate the incoming request data
            $validatedData = Validator::make($request->all(),[
                'admission_no' => 'required',
                'amount' => 'required|numeric',
            ], 
            [
                'admission_no.required' => 'Admission Number is required.',
                'amount.required' => 'Amount is required.',
                'amount.numeric' => 'Amount should be a number.',
                
            ]);

           if($validatedData->fails()){
                return $this->responseError("validation_error", $validatedData->errors()->first(), 400);
            }
            //createcurrent date
            

            // Create the student using the validated data
            return $this->responseSuccess(FeesCalculationRepository::reviceSercharge($request->all()), 'Revice Sercharge recorde create successfully.');
        }catch (Exception $exception) {
            // Handle other exceptions
            return $this->responseError([], $exception->getMessage(), $exception->getCode());
        }
        
    }
}
