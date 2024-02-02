<?php

namespace App\Http\Controllers;
use Exception;
use App\Http\Requests\UserRoleCreateRequest;
use App\Http\Requests\UserRoleUpdateRequest;
use App\Repositories\UserRoleRepository;
use App\Traits\ResponseTrait;
use Illuminate\Http\JsonResponse;
use App\Models\UserRole;
use Illuminate\Http\Request;

class UserRoleController extends Controller
{
    use ResponseTrait;

    public $userRoleRepository;

    public function __construct(UserRoleRepository $userRoleRepository)
    {
        $this->userRoleRepository = $userRoleRepository;
    }


    public function index(): JsonResponse
    {
        try {
            return $this->responseSuccess($this->userRoleRepository->getAll(request()->all()), 'User role fetched successfully.');
        } catch (Exception $exception) {
            return $this->responseError([], $exception->getMessage(), $exception->getCode());
        }
    }

  
    public function store(UserRoleCreateRequest $request): JsonResponse
    {
        try {
            return $this->responseSuccess($this->userRoleRepository->create($request->all()), 'User role created successfully.');
        } catch (Exception $exception) {
            return $this->responseError([], $exception->getMessage(), $exception->getCode());
        }
    }


    public function show(int $id): JsonResponse
    {
        try {
            return $this->responseSuccess($this->userRoleRepository->getById($id), 'User role fetched successfully.');
        } catch (Exception $exception) {
            return $this->responseError([], $exception->getMessage(), $exception->getCode());
        }
    }

    
    public function update(UserRoleUpdateRequest $request, int $id): JsonResponse
    {
        try {
            return $this->responseSuccess($this->userRoleRepository->update($id, $request->all()), 'User role updated successfully.');
        } catch (Exception $exception) {
            return $this->responseError([], $exception->getMessage(), $exception->getCode());
        }
    }

    
    public function destroy(int $id): JsonResponse
    {
        try {
            return $this->responseSuccess($this->userRoleRepository->delete($id), 'User role deleted successfully.');
        } catch (Exception $exception) {
            return $this->responseError([], $exception->getMessage(), $exception->getCode());
        }
    }
}





