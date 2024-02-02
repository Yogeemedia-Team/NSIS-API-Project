<?php

namespace App\Http\Controllers;
use Exception;
use App\Http\Requests\UserLevelCreateRequest;
use App\Http\Requests\UserLevelUpdateRequest;
use App\Repositories\UserLevelRepository;
use App\Traits\ResponseTrait;
use Illuminate\Http\JsonResponse;
use App\Models\UserLevel;
use Illuminate\Http\Request;

class UserLevelController extends Controller
{
    use ResponseTrait;

    public $userLevelRepository;

    public function __construct(UserLevelRepository $userLevelRepository)
    {
        $this->userLevelRepository = $userLevelRepository;
    }


    
    public function index(): JsonResponse
    {
        try {
            return $this->responseSuccess($this->userLevelRepository->getAll(request()->all()), 'User Level fetched successfully.');
        } catch (Exception $exception) {
            return $this->responseError([], $exception->getMessage(), $exception->getCode());
        }
    }

    
    public function store(UserLevelCreateRequest $request): JsonResponse
    {
        try {
            return $this->responseSuccess($this->userLevelRepository->create($request->all()), 'User Level created successfully.');
        } catch (Exception $exception) {
            return $this->responseError([], $exception->getMessage(), $exception->getCode());
        }
    }

    
    public function show(int $id): JsonResponse
    {
        try {
            return $this->responseSuccess($this->userLevelRepository->getById($id), 'User Level fetched successfully.');
        } catch (Exception $exception) {
            return $this->responseError([], $exception->getMessage(), $exception->getCode());
        }
    }

    
    public function update(UserLevelUpdateRequest $request, int $id): JsonResponse
    {
        try {
            return $this->responseSuccess($this->userLevelRepository->update($id, $request->all()), 'User Level updated successfully.');
        } catch (Exception $exception) {
            return $this->responseError([], $exception->getMessage(), $exception->getCode());
        }
    }

    
    public function destroy(int $id): JsonResponse
    {
        try {
            return $this->responseSuccess($this->userLevelRepository->delete($id), 'User Level deleted successfully.');
        } catch (Exception $exception) {
            return $this->responseError([], $exception->getMessage(), $exception->getCode());
        }
    }
}





