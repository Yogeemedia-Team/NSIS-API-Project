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
            return $this->responseSuccess($this->studentRepository->create($request->all()), 'User created successfully.');
        } catch (Exception $exception) {
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

