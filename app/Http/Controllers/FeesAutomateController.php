<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Exception;
use App\Repositories\FeesCalculationRepository;
use App\Traits\ResponseTrait;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\UserPaymentCreateRequest;

class FeesAutomateController extends Controller
{
        use ResponseTrait;

    public $feescalculationRepository;

    public function __construct(FeesCalculationRepository $feescalculationRepository)
    {
        $this->feescalculationRepository = $feescalculationRepository;
    }

    public function monthly_fee(): JsonResponse
    {
        try {
            return $this->responseSuccess($this->feescalculationRepository->monthlyFee(), 'Monthly fee generate successfully.');
        } catch (Exception $exception) {
            return $this->responseError([], $exception->getMessage(), $exception->getCode());
        }
    }

    public function invoice_generate(): JsonResponse
    {
        try {
            return $this->responseSuccess($this->feescalculationRepository->invoice_generate(), 'Invoice generate successfully.');
        } catch (Exception $exception) {
            return $this->responseError([], $exception->getMessage(), $exception->getCode());
        }
    }

      
}
