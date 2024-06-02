<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Traits\ResponseTrait;
use App\Repositories\ReportRepository;
use Exception;

use Illuminate\Support\Facades\Validator;

class ReportController extends Controller {
    use ResponseTrait;

    public $reportRepository;

    public function __construct(ReportRepository $reportRepository)
    {
        $this->reportRepository = $reportRepository;
    }

    
    public function payment_report(Request $request): JsonResponse
    {
        try {
            // Validate the incoming request data
            $validatedData = Validator::make($request->all(),[
                'admission_no' => 'required',
            ], 
            [
                'admission_no.required' => 'Admission Number is required.',
            ]);

           if($validatedData->fails()){
                return $this->responseError("validation_error", $validatedData->errors()->first(), 400);
            }
            return $this->responseSuccess(
                $this->reportRepository->payment_report($request->all()),
                'Payment report fetch successfully.'
            );
        } catch (Exception $exception) {
            return $this->responseError([], $exception->getMessage(), $exception->getCode());
        }
       
           
    }

    public function transaction_report(Request $request): JsonResponse
    {
        try {
            // Validate the incoming request data
            $validatedData = Validator::make($request->all(),[
                'admission_no' => 'required',
            ], 
            [
                'admission_no.required' => 'Admission Number is required.',
            ]);

           if($validatedData->fails()){
                return $this->responseError("validation_error", $validatedData->errors()->first(), 400);
            }
            return $this->responseSuccess(
                $this->reportRepository->transaction_report($request->all()),
                'Transaction report fetch successfully.'
            );
        } catch (Exception $exception) {
            return $this->responseError([], $exception->getMessage(), $exception->getCode());
        }
       
           
    }
    
    public function outstanding_report(Request $request): JsonResponse
    {
        try {
            // Validate the incoming request data
            $validatedData = Validator::make($request->all(),[
                'admission_no' => 'required',
            ], 
            [
                'admission_no.required' => 'Admission Number is required.',
            ]);

           if($validatedData->fails()){
                return $this->responseError("validation_error", $validatedData->errors()->first(), 400);
            }
            return $this->responseSuccess(
                $this->reportRepository->outstanding_report($request->all()),
                'Outstanding report fetch successfully.'
            );
        } catch (Exception $exception) {
            return $this->responseError([], $exception->getMessage(), $exception->getCode());
        }
       
    }

    public function payment_delaied_report(Request $request): JsonResponse
    {
        try {
            return $this->responseSuccess(
                $this->reportRepository->payment_delaied_report($request->all()),
                'Payment delaied report fetch successfully.'
            );
        } catch (Exception $exception) {
            return $this->responseError([], $exception->getMessage(), $exception->getCode());
        }
       
    }

    public function grade_class_student_report(Request $request): JsonResponse
    {
        try {
            // Validate the incoming request data
            $validatedData = Validator::make($request->all(),[
                'year' => 'required',
            ], 
            [
                'year.required' => 'Year is required.',
            ]);

           if($validatedData->fails()){
                return $this->responseError("validation_error", $validatedData->errors()->first(), 400);
            }
            return $this->responseSuccess(
                $this->reportRepository->grade_class_student_report($request->all()),
                'Students fetch successfully.'
            );
        } catch (Exception $exception) {
            return $this->responseError([], $exception->getMessage(), $exception->getCode());
        }
       
    }

    
    
    public function student_extra_curriculars(Request $request): JsonResponse
    {
        try {
            // Validate the incoming request data
            $validatedData = Validator::make($request->all(),[
                'extra_curricular_id' => 'required',
            ], 
            [
                'extra_curricular_id.required' => 'Extra Curricular_id is required.',
            ]);

           if($validatedData->fails()){
                return $this->responseError("validation_error", $validatedData->errors()->first(), 400);
            }
            return $this->responseSuccess(
                $this->reportRepository->student_extra_curriculars($request->all()),
                'Students fetch successfully.'
            );
        } catch (Exception $exception) {
            return $this->responseError([], $exception->getMessage(), $exception->getCode());
        }
       
    }
    

    public function income_report(Request $request): JsonResponse
    {
        try {

            $validatedData = Validator::make($request->all(),[
                'from_date' => 'required',
                'to_date' => 'required',
            ], 
            [
                'from_date.required' => 'From Date is required.',
                'to_date.required' => 'To Date is required.',
            ]);

           if($validatedData->fails()){
                return $this->responseError("validation_error", $validatedData->errors()->first(), 400);
            }
            return $this->responseSuccess(
                $this->reportRepository->income_report($request->all()),
                'Students fetch successfully.'
            );
        } catch (Exception $exception) {
            return $this->responseError([], $exception->getMessage(), $exception->getCode());
        }
       
    }
    

    
    
}
