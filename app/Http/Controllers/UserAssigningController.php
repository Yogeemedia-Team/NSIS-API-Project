<?php

namespace App\Http\Controllers;
use Exception;
use App\Http\Requests\UserAssigneeCreateRequest;
use App\Http\Requests\UserAssigneeUpdateRequest;
use App\Repositories\UserAssigneeRepository;
use App\Traits\ResponseTrait;
use Illuminate\Http\JsonResponse;
use App\Models\UserAssigning;
use Illuminate\Http\Request;

class UserAssigningController extends Controller
{
    use ResponseTrait;

    public $userAssigneeRepository;

    public function __construct(UserAssigneeRepository $userAssigneeRepository)
    {
        $this->userAssigneeRepository = $userAssigneeRepository;
    }


    
    public function index(): JsonResponse
    {
        try {
            return $this->responseSuccess($this->userAssigneeRepository->getAll(request()->all()), 'User Assign Permission fetched successfully.');
        } catch (Exception $exception) {
            return $this->responseError([], $exception->getMessage(), $exception->getCode());
        }
    }

 
    public function store(UserAssigneeCreateRequest $request): JsonResponse
    {
        try {
            return $this->responseSuccess($this->userAssigneeRepository->create($request->all()), 'User Assign Permission created successfully.');
        } catch (Exception $exception) {
            return $this->responseError([], $exception->getMessage(), $exception->getCode());
        }
    }

    
    public function show(int $id): JsonResponse
    {
        try {
            return $this->responseSuccess($this->userAssigneeRepository->getById($id), 'User Assign Permission fetched successfully.');
        } catch (Exception $exception) {
            return $this->responseError([], $exception->getMessage(), $exception->getCode());
        }
    }

    
    public function update(UserAssigneeUpdateRequest $request, int $id): JsonResponse
    {
        try {
            return $this->responseSuccess($this->userAssigneeRepository->update($id, $request->all()), 'User Assign Permission updated successfully.');
        } catch (Exception $exception) {
            return $this->responseError([], $exception->getMessage(), $exception->getCode());
        }
    }

  
    public function destroy(int $id): JsonResponse
    {
        try {
            return $this->responseSuccess($this->userAssigneeRepository->delete($id), 'User Assign Permission deleted successfully.');
        } catch (Exception $exception) {
            return $this->responseError([], $exception->getMessage(), $exception->getCode());
        }
    }
}

