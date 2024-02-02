<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Exception;
use App\Http\Requests\MasterExtracurricularCreateRequest;
use App\Http\Requests\MasterExtracurricularUpdateRequest;
use App\Repositories\StudentPaymentRepository;
use App\Traits\ResponseTrait;
use Illuminate\Http\JsonResponse;

class StudentPaymentController extends Controller
{
    use ResponseTrait;

    public $studentpaymentRepository;

    public function __construct(StudentPaymentRepository $studentpaymentRepository)
    {
        $this->studentpaymentRepository = $studentpaymentRepository;
    }


    public function index(): JsonResponse
    {
        try {
            return $this->responseSuccess($this->studentpaymentRepository->getAll(request()->all()), 'Student payment fetched successfully.');
        } catch (Exception $exception) {
            return $this->responseError([], $exception->getMessage(), $exception->getCode());
        }
    }

    public function show($id): JsonResponse
    {
        try {
            return $this->responseSuccess($this->studentpaymentRepository->getById($id), 'Student payment fetched successfully.');
        } catch (Exception $exception) {
            return $this->responseError([], $exception->getMessage(), $exception->getCode());
        }
    }


     public function update(MasterExtracurricularUpdateRequest $request, int $id): JsonResponse
    {
        try {
            return $this->responseSuccess($this->studentpaymentRepository->update($id, $request->all()), 'Student payment updated successfully.');
        } catch (Exception $exception) {
            return $this->responseError([], $exception->getMessage(), $exception->getCode());
        }
    }


      public function destroy(int $id): JsonResponse
    {
        try {
            return $this->responseSuccess($this->studentpaymentRepository->delete($id), 'Student payment deleted successfully.');
        } catch (Exception $exception) {
            return $this->responseError([], $exception->getMessage(), $exception->getCode());
        }
    }
}
