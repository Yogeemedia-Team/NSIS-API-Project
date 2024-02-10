<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;
use App\Repositories\FeesCalculationRepository;
use Exception;
use App\Traits\ResponseTrait;
use Illuminate\Http\JsonResponse;
 
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
}
