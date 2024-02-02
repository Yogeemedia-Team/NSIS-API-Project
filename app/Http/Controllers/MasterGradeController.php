<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Exception;
use App\Http\Requests\MasterGradeCreateRequest;
use App\Http\Requests\MasterGradeUpdateRequest;
use App\Repositories\MasterGradeRepository;
use App\Traits\ResponseTrait;
use Illuminate\Http\JsonResponse;

class MasterGradeController extends Controller
{
    use ResponseTrait;

    public $mastergradeRepository;

    public function __construct(MasterGradeRepository $mastergradeRepository)
    {
        $this->mastergradeRepository = $mastergradeRepository;
    }


    public function index(): JsonResponse
    {
        try {
            return $this->responseSuccess($this->mastergradeRepository->getAll(request()->all()), 'Grade fetched successfully.');
        } catch (Exception $exception) {
            return $this->responseError([], $exception->getMessage(), $exception->getCode());
        }
    }


    public function store(MasterGradeCreateRequest $request): JsonResponse
    {
        try {
            return $this->responseSuccess($this->mastergradeRepository->create($request->all()), 'Grade created successfully.');
        } catch (Exception $exception) {
            return $this->responseError([], $exception->getMessage(), $exception->getCode());
        }
    }


    public function show(int $id): JsonResponse
    {
        try {
            return $this->responseSuccess($this->mastergradeRepository->getById($id), 'Grade fetched successfully.');
        } catch (Exception $exception) {
            return $this->responseError([], $exception->getMessage(), $exception->getCode());
        }
    }


     public function update(MasterGradeUpdateRequest $request, int $id): JsonResponse
    {
        try {
            return $this->responseSuccess($this->mastergradeRepository->update($request->all(), $id), 'grade updated successfully.');
        } catch (Exception $exception) {
            return $this->responseError([], $exception->getMessage(), $exception->getCode());
        }
    }


      public function destroy(int $id): JsonResponse
    {
        try {
            return $this->responseSuccess($this->mastergradeRepository->delete($id), 'Grade deleted successfully.');
        } catch (Exception $exception) {
            return $this->responseError([], $exception->getMessage(), $exception->getCode());
        }
    }
}
