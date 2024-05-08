<?php

namespace App\Http\Controllers;
use Exception;
use App\Http\Requests\UserCreateRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Repositories\UserRepository;
use App\Traits\ResponseTrait;
use Illuminate\Http\JsonResponse;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    use ResponseTrait;

    public $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }
    


    
    public function index(): JsonResponse
    {
        try {
            return $this->responseSuccess($this->userRepository->getAll(request()->all()), 'User fetched successfully.');
        } catch (Exception $exception) {
            return $this->responseError([], $exception->getMessage(), $exception->getCode());
        }
    }

    public function student_lists(): JsonResponse
    {
        try {
            return $this->responseSuccess($this->userRepository->studentLists(request()->all()), 'Student lists fetched successfully.');
        } catch (Exception $exception) {
            return $this->responseError([], $exception->getMessage(), $exception->getCode());
        }
    }
    
    public function store(UserCreateRequest $request): JsonResponse
    {
        try {
            return $this->responseSuccess($this->userRepository->create($request->all()), 'User created successfully.');
        } catch (Exception $exception) {
            return $this->responseError([], $exception->getMessage(), $exception->getCode());
        }
    }

    
    public function show(int $id): JsonResponse
    {
        try {
            return $this->responseSuccess($this->userRepository->getById($id), 'User fetched successfully.');
        } catch (Exception $exception) {
            return $this->responseError([], $exception->getMessage(), $exception->getCode());
        }
    }

    
    public function update(UserUpdateRequest $request, int $id): JsonResponse
    {
        try {
            return $this->responseSuccess($this->userRepository->update($id, $request->all()), 'User updated successfully.');
        } catch (Exception $exception) {
            return $this->responseError([], $exception->getMessage(), $exception->getCode());
        }
    }

    
    public function destroy(int $id): JsonResponse
    {
        try {
            return $this->responseSuccess($this->userRepository->delete($id), 'User deleted successfully.');
        } catch (Exception $exception) {
            return $this->responseError([], $exception->getMessage(), $exception->getCode());
        }
    }
}
