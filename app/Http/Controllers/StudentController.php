<?php

namespace App\Http\Controllers;
use Exception;
use App\Http\Requests\StudentCreateRequest;
use App\Http\Requests\StudentUpdateRequest;
use App\Repositories\StudentRepository;
use App\Traits\ResponseTrait;
use Illuminate\Http\JsonResponse;
use App\Models\UserActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;



class StudentController extends Controller
{
    use ResponseTrait;

    public $studentRepository;

    public function __construct(StudentRepository $studentRepository)
    {
        $this->studentRepository = $studentRepository;
    }


    
    public function index(): JsonResponse
    {
        try {
            return $this->responseSuccess($this->studentRepository->getAll(request()->all()), 'User fetched successfully.');
        } catch (Exception $exception) {
            return $this->responseError([], $exception->getMessage(), $exception->getCode());
        }
    }

  
    public function store(StudentCreateRequest $request): JsonResponse
    {
        try {
            // Validate the incoming request data
            $validatedData = Validator::make($request->all(),[
                'sd_admission_no' => 'required|string|unique:student_service.student_details,sd_admission_no',
                'sd_email_address' => 'required|email|unique:student_service.student_details,sd_email_address',
            ], 
            [
                'sd_admission_no.required' => 'The admission number is required.',
                'sd_admission_no.string' => 'The admission number must be a string.',
                
                'sd_email_address.required' => 'The email address is required.',
                'sd_email_address.email' => 'The email address must be a valid email format.',
                'sd_email_address.unique' => 'The email address has already been taken.',
                'sd_admission_no.unique' => 'The admission no has already been taken.',
            ]);
            if($validatedData->fails()){
                return $this->responseError("validation_error", $validatedData->errors()->first(), 400);
            }
            // Create the student using the validated data
            return $this->responseSuccess($this->studentRepository->create($request->all()), 'User created successfully.');
        }catch (Exception $exception) {
            // Handle other exceptions
            return $this->responseError([], $exception->getMessage(), $exception->getCode());
        }
       
    }

 
    public function show($id): JsonResponse
    {
        try {
            return $this->responseSuccess($this->studentRepository->getById($id), 'User fetched successfully.');
        } catch (Exception $exception) {
            return $this->responseError([], $exception->getMessage(), $exception->getCode());
        }
    }


    public function update(StudentUpdateRequest $request, $id): JsonResponse
    {
        try {
            return $this->responseSuccess($this->studentRepository->update($request->all(), $id), 'User updated successfully.');
        } catch (Exception $exception) {
            return $this->responseError([], $exception->getMessage(), $exception->getCode());
        }
    }

   
    public function destroy($id): JsonResponse
    {
        try {
            return $this->responseSuccess($this->studentRepository->delete($id), 'User deleted successfully.');
        } catch (Exception $exception) {
            return $this->responseError([], $exception->getMessage(), $exception->getCode());
        }
    }
}

