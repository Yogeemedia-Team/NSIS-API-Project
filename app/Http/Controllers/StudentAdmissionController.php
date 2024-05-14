<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Exception;
use App\Http\Requests\StudentAdmissionRequest;
use App\Repositories\StudentAdminssionRepository;
use App\Traits\ResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class StudentAdmissionController extends Controller
{
    use ResponseTrait;

    public $studentAdminssionRepository;

    public function __construct(StudentAdminssionRepository $studentAdminssionRepository)
    {
        $this->studentAdminssionRepository = $studentAdminssionRepository;
    }


    public function index(Request $request): JsonResponse
    {
        try {
            return $this->responseSuccess($this->studentAdminssionRepository->getAll($request->all()), 'Student Admintions fetched successfully.');
        } catch (Exception $exception) {
            return $this->responseError([], $exception->getMessage(), $exception->getCode());
        }
    }


    public function create(StudentAdmissionRequest $request)
    {
    
        try {
            $validatedData = Validator::make($request->all(),[
                'admission_no' => 'required',
                'total_amount' => 'required|numeric',
                'no_of_instalments' => 'required|numeric',
                'admission_instalments' => 'required',
            ], 
            [
                'admission_no.required' => 'Admission no is required.',
                'total_amount.required' => 'Total amount is required.',
                'no_of_instalments.required' => 'No of instalments is required.',
                'admission_instalments.required' => 'Admission instalments is required.',
                
            ]);

           if($validatedData->fails()){
                return $this->responseError("validation_error", $validatedData->errors()->first(), 400);
            }

            return $this->responseSuccess($this->studentAdminssionRepository->store($request->all()), 'Student Admintions created successfully.');
        } catch (Exception $exception) {
            return $this->responseError([], $exception->getMessage(), $exception->getCode());
        }
    }

    
    public function update(StudentAdmissionRequest $request)
    {
    
        try {
            $validatedData = Validator::make($request->all(),[
                'id' => 'required|numeric',
                'paid_date' => 'required'
            ]);

           if($validatedData->fails()){
                return $this->responseError("validation_error", $validatedData->errors()->first(), 400);
            }

            return $this->responseSuccess($this->studentAdminssionRepository->update($request->all()), 'Student Admintions Instalment update successfully.');
        } catch (Exception $exception) {
            return $this->responseError([], $exception->getMessage(), $exception->getCode());
        }
    }


    public function show(int $id): JsonResponse
    {
        try {
            return $this->responseSuccess($this->studentAdminssionRepository->getById($id), 'Student Admintion fetched successfully.');
        } catch (Exception $exception) {
            return $this->responseError([], $exception->getMessage(), $exception->getCode());
        }
    }
}