<?php

namespace App\Http\Controllers;
use Exception;
use App\Http\Requests\MasterClassCreateRequest;
use App\Http\Requests\MasterClassUpdateRequest;
use App\Repositories\MasterClassRepository;
use App\Traits\ResponseTrait;
use Illuminate\Http\JsonResponse;


class MasterClassController extends Controller
{
    use ResponseTrait;

    public $masterclassRepository;

    public function __construct(MasterClassRepository $masterclassRepository)
    {
        $this->masterclassRepository = $masterclassRepository;
    }


    public function index(): JsonResponse
    {
        try {
            return $this->responseSuccess($this->masterclassRepository->getAll(request()->all()), 'Master class fetched successfully.');
        } catch (Exception $exception) {
            return $this->responseError([], $exception->getMessage(), $exception->getCode());
        }
    }


    public function store(MasterClassCreateRequest $request): JsonResponse
    {
        try {
            return $this->responseSuccess($this->masterclassRepository->create($request->all()), 'Master class created successfully.');
        } catch (Exception $exception) {
            return $this->responseError([], $exception->getMessage(), $exception->getCode());
        }
    }


    public function show(int $id): JsonResponse
    {
        try {
            return $this->responseSuccess($this->masterclassRepository->getById($id), 'Master class fetched successfully.');
        } catch (Exception $exception) {
            return $this->responseError([], $exception->getMessage(), $exception->getCode());
        }
    }


     public function update(MasterClassUpdateRequest $request, int $id): JsonResponse
    {
        try {
            return $this->responseSuccess($this->masterclassRepository->update($id, $request->all()), 'Master class updated successfully.');
        } catch (Exception $exception) {
            return $this->responseError([], $exception->getMessage(), $exception->getCode());
        }
    }


      public function destroy(int $id): JsonResponse
    {
        try {
            return $this->responseSuccess($this->masterclassRepository->delete($id), 'Master class deleted successfully.');
        } catch (Exception $exception) {
            return $this->responseError([], $exception->getMessage(), $exception->getCode());
        }
    }
}
