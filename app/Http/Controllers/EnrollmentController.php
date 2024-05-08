<?php

namespace App\Http\Controllers;

use App\Http\Requests\EnrollmentsCreateRequest;
use App\Http\Requests\EnrollmentsUpdateRequest;
use Exception;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use App\Repositories\UserEnrollmentRepository;
use Illuminate\Http\JsonResponse;

class EnrollmentController extends Controller
{
    use ResponseTrait;

    public $userEnrollmentRepository;

    public function __construct(UserEnrollmentRepository $userEnrollmentRepository)
    {
        $this->userEnrollmentRepository = $userEnrollmentRepository;
    }


    
    public function index(): JsonResponse
    {
        try {
            return $this->responseSuccess($this->userEnrollmentRepository->getAll(request()->all()), 'Enrollment users successfully.');
        } catch (Exception $exception) {
            return $this->responseError([], $exception->getMessage(), $exception->getCode());
        }
    }

  
    public function store(EnrollmentsCreateRequest $request): JsonResponse
    {
        try {
            return $this->responseSuccess($this->userEnrollmentRepository->create($request->all()), 'Enrollment user created successfully.');
        } catch (Exception $exception) {
            return $this->responseError([], $exception->getMessage(), $exception->getCode());
        }
    }

 
    public function show($id): JsonResponse
    {
        try {
            return $this->responseSuccess($this->userEnrollmentRepository->getById($id), 'Enrollment user fetched successfully.');
        } catch (Exception $exception) {
            return $this->responseError([], $exception->getMessage(), $exception->getCode());
        }
    }


    public function update(EnrollmentsUpdateRequest $request, $id): JsonResponse
    {
        try {
            return $this->responseSuccess($this->userEnrollmentRepository->update($request->all(), $id), 'Enrollment user updated successfully.');
        } catch (Exception $exception) {
            return $this->responseError([], $exception->getMessage(), $exception->getCode());
        }
    }

   
    public function destroy($id): JsonResponse
    {
        try {
            return $this->responseSuccess($this->userEnrollmentRepository->delete($id), 'Enrollment user deleted successfully.');
        } catch (Exception $exception) {
            return $this->responseError([], $exception->getMessage(), $exception->getCode());
        }
    }
}
