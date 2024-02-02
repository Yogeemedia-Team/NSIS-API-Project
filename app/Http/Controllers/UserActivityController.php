<?php

namespace App\Http\Controllers;
use Exception;
use App\Http\Requests\UserActivityCreateRequest;
use App\Http\Requests\UserActivityUpdateRequest;
use App\Repositories\UserActivityRepository;
use App\Traits\ResponseTrait;
use Illuminate\Http\JsonResponse;
use App\Models\UserActivity;
use Illuminate\Http\Request;

class UserActivityController extends Controller
{
    use ResponseTrait;

    public $userActivityRepository;

    public function __construct(UserActivityRepository $userActivityRepository)
    {
        $this->userActivityRepository = $userActivityRepository;
    }


   
    public function index(): JsonResponse
    {
        try {
            return $this->responseSuccess($this->userActivityRepository->getAll(request()->all()), 'User Activity fetched successfully.');
        } catch (Exception $exception) {
            return $this->responseError([], $exception->getMessage(), $exception->getCode());
        }
    }

    public function store(UserActivityCreateRequest $request): JsonResponse
    {
        try {
            return $this->responseSuccess($this->userActivityRepository->create($request->all()), 'User Activity created successfully.');
        } catch (Exception $exception) {
            return $this->responseError([], $exception->getMessage(), $exception->getCode());
        }
    }

    
    public function show(int $id): JsonResponse
    {
        try {
            return $this->responseSuccess($this->userActivityRepository->getById($id), 'User Activity fetched successfully.');
        } catch (Exception $exception) {
            return $this->responseError([], $exception->getMessage(), $exception->getCode());
        }
    }

    
    public function update(UserActivityUpdateRequest $request, int $id): JsonResponse
    {
        try {
            return $this->responseSuccess($this->userActivityRepository->update($id, $request->all()), 'User Activity updated successfully.');
        } catch (Exception $exception) {
            return $this->responseError([], $exception->getMessage(), $exception->getCode());
        }
    }

    
    public function destroy(int $id): JsonResponse
    {
        try {
            return $this->responseSuccess($this->userActivityRepository->delete($id), 'User Activity deleted successfully.');
        } catch (Exception $exception) {
            return $this->responseError([], $exception->getMessage(), $exception->getCode());
        }
    }
}

