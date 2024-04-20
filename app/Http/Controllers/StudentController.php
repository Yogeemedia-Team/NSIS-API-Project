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
                'monthly_fee' => 'required|numeric',
               // 'sd_email_address' => 'required|email|unique:student_service.student_details,sd_email_address',
            ], 
            [
                'sd_admission_no.required' => 'The admission number is required.',
                'sd_admission_no.string' => 'The admission number must be a string.',

                'monthly_fee.required' => 'The Monthly Free is required.',
                'monthly_fee.numeric' => 'The Monthly Free must be an amount.',
                
                'sd_email_address.required' => 'The email address is required.',
                'sd_email_address.email' => 'The email address must be a valid email format.',
                'sd_email_address.unique' => 'The email address has already been taken.',
                'sd_admission_no.unique' => 'The admission no has already been taken.',
            ]);

            //ALTER TABLE `student_details` CHANGE `id` `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT, CHANGE `sd_name_in_full` `sd_name_in_full` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL, CHANGE `sd_gender` `sd_gender` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL, CHANGE `sd_date_of_birth` `sd_date_of_birth` DATE NULL DEFAULT NULL, CHANGE `sd_religion` `sd_religion` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL, CHANGE `sd_ethnicity` `sd_ethnicity` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL, CHANGE `sd_birth_certificate_number` `sd_birth_certificate_number` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL, CHANGE `sd_admission_date` `sd_admission_date` DATE NULL DEFAULT NULL, CHANGE `sd_admission_payment_amount` `sd_admission_payment_amount` DECIMAL(10,2) NULL DEFAULT NULL, CHANGE `sd_no_of_installments` `sd_no_of_installments` INT NULL DEFAULT NULL;
           // ALTER TABLE `student_parents` CHANGE `id` `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT, CHANGE `sp_father_first_name` `sp_father_first_name` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL, CHANGE `sp_father_last_name` `sp_father_last_name` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL, CHANGE `sp_father_nic` `sp_father_nic` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL, CHANGE `sp_father_higher_education_qualification` `sp_father_higher_education_qualification` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL, CHANGE `sp_father_occupation` `sp_father_occupation` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL, CHANGE `sp_mother_first_name` `sp_mother_first_name` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL, CHANGE `sp_mother_last_name` `sp_mother_last_name` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL, CHANGE `sp_mother_nic` `sp_mother_nic` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL, CHANGE `sp_mother_higher_education_qualification` `sp_mother_higher_education_qualification` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL;
           //ALTER TABLE `student_siblings` CHANGE `id` `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT, CHANGE `ss_details` `ss_details` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL;
           
           if($validatedData->fails()){
                return $this->responseError("validation_error", $validatedData->errors()->first(), 400);
            }
            //createcurrent date
            

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

