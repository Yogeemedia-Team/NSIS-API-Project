<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Exception;
use App\Http\Requests\YearGradeClassCreateRequest;
use App\Http\Requests\YearGradeClassUpdateRequest;
use App\Repositories\YearGradeClassRepository;
use App\Traits\ResponseTrait;
use Illuminate\Http\JsonResponse;

class YearClassGradeController extends Controller
{
    use ResponseTrait;

    public $yeargradeclassRepository;

    public function __construct(YearGradeClassRepository $yeargradeclassRepository)
    {
        $this->yeargradeclassRepository = $yeargradeclassRepository;
    }


    public function index(): JsonResponse
    {
        try {
            return $this->responseSuccess($this->yeargradeclassRepository->getAll(request()->all()), 'Year Grade Class fetched successfully.');
        } catch (Exception $exception) {
            return $this->responseError([], $exception->getMessage(), $exception->getCode());
        }
    }


    public function store(YearGradeClassCreateRequest $request): JsonResponse
    {
        try {
            return $this->responseSuccess($this->yeargradeclassRepository->create($request->all()), 'Year Grade Class created successfully.');
        } catch (Exception $exception) {
            return $this->responseError([], $exception->getMessage(), $exception->getCode());
        }
    }


    public function show(int $id): JsonResponse
    {
        try {
            return $this->responseSuccess($this->yeargradeclassRepository->getById($id), 'Year Grade Class fetched successfully.');
        } catch (Exception $exception) {
            return $this->responseError([], $exception->getMessage(), $exception->getCode());
        }
    }


     public function update(YearGradeClassUpdateRequest $request, int $id): JsonResponse
    {
        try {
            return $this->responseSuccess($this->yeargradeclassRepository->update($id, $request->all()), 'Year Grade Class updated successfully.');
        } catch (Exception $exception) {
            return $this->responseError([], $exception->getMessage(), $exception->getCode());
        }
    }


      public function destroy(int $id): JsonResponse
    {
        try {
            return $this->responseSuccess($this->yeargradeclassRepository->delete($id), 'Year Grade Class deleted successfully.');
        } catch (Exception $exception) {
            return $this->responseError([], $exception->getMessage(), $exception->getCode());
        }
    }
}
