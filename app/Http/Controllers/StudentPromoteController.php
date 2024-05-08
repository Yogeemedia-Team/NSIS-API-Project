<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\StudentPromoteRepository;
use App\Traits\ResponseTrait;
use App\Http\Requests\StudentPromoteRequest;
use Illuminate\Http\JsonResponse;
use Exception;
use Illuminate\Support\Facades\Validator;

class StudentPromoteController extends Controller
{
    use ResponseTrait;

    public $studentPromoteRepository;

    public function __construct(StudentPromoteRepository $studentPromoteRepository)
    {
        $this->studentPromoteRepository = $studentPromoteRepository;
    }

    public function store(StudentPromoteRequest $request): JsonResponse
    {
       dd($request->all());
        try {
            // Validate the incoming request data
            $validatedData = Validator::make($request->all(),[
                'prev_sd_year_grade_class_id' => 'required',
                'promoted_sd_year_grade_class_id' => 'required',
                'student_data' => 'required',
            ], 
            [
                'prev_sd_year_grade_class_id.required' => 'Current Year Grade Class is required.',
                'promoted_sd_year_grade_class_id.required' => 'Next Year Grade Class is required.',
                'student_data.required' => 'The admission number is required.',
                
            ]);

           if($validatedData->fails()){
                return $this->responseError("validation_error", $validatedData->errors()->first(), 400);
            }
            //createcurrent date
            

            // Create the student using the validated data
            return $this->responseSuccess($this->studentPromoteRepository->promote($request->all()), 'Students Promote successfully.');
        }catch (Exception $exception) {
            // Handle other exceptions
            return $this->responseError([], $exception->getMessage(), $exception->getCode());
        }
       
    }
}
