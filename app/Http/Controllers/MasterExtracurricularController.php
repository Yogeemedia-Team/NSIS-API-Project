<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Exception;
use App\Http\Requests\MasterExtracurricularCreateRequest;
use App\Http\Requests\MasterExtracurricularUpdateRequest;
use App\Repositories\MasterExtracurricularRepository;
use App\Traits\ResponseTrait;
use Illuminate\Http\JsonResponse;

class MasterExtracurricularController extends Controller
{
    use ResponseTrait;

    public $masterextracurricularRepository;

    public function __construct(MasterExtracurricularRepository $masterextracurricularRepository)
    {
        $this->masterextracurricularRepository = $masterextracurricularRepository;
    }


    public function index(): JsonResponse
    {
        try {
            return $this->responseSuccess($this->masterextracurricularRepository->getAll(request()->all()), 'Extracurricular fetched successfully.');
        } catch (Exception $exception) {
            return $this->responseError([], $exception->getMessage(), $exception->getCode());
        }
    }


    public function store(MasterExtracurricularCreateRequest $request): JsonResponse
    {
        try {
            return $this->responseSuccess($this->masterextracurricularRepository->create($request->all()), 'Extracurricular created successfully.');
        } catch (Exception $exception) {
            return $this->responseError([], $exception->getMessage(), $exception->getCode());
        }
    }


    public function show(int $id): JsonResponse
    {
        try {
            return $this->responseSuccess($this->masterextracurricularRepository->getById($id), 'Extracurricular fetched successfully.');
        } catch (Exception $exception) {
            return $this->responseError([], $exception->getMessage(), $exception->getCode());
        }
    }


     public function update(MasterExtracurricularUpdateRequest $request, int $id): JsonResponse
    {
        try {
            return $this->responseSuccess($this->masterextracurricularRepository->update($id, $request->all()), 'Extracurricular updated successfully.');
        } catch (Exception $exception) {
            return $this->responseError([], $exception->getMessage(), $exception->getCode());
        }
    }


      public function destroy(int $id): JsonResponse
    {
        try {
            return $this->responseSuccess($this->masterextracurricularRepository->delete($id), 'Extracurricular deleted successfully.');
        } catch (Exception $exception) {
            return $this->responseError([], $exception->getMessage(), $exception->getCode());
        }
    }
}
