<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Exception;
use App\Repositories\FeesCalculationRepository;
use App\Traits\ResponseTrait;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\UserPaymentCreateRequest;

class FeesCalculationController extends Controller
{
    use ResponseTrait;

    public $feescalculationRepository;

    public function __construct(FeesCalculationRepository $feescalculationRepository)
    {
        $this->feescalculationRepository = $feescalculationRepository;
    }

    
    public function user_payments($id): JsonResponse
    {
        try {
            return $this->responseSuccess($this->feescalculationRepository->user_payments($id), 'User payments fetch successfully.');
        } catch (Exception $exception) {
            return $this->responseError([], $exception->getMessage(), $exception->getCode());
        }
    }

    public function user_payment_update(UserPaymentCreateRequest $request): JsonResponse
    {
        try {
            return $this->responseSuccess($this->feescalculationRepository->user_payment_update($request->all()), 'Payment successfully.');
        } catch (Exception $exception) {
            return $this->responseError([], $exception->getMessage(), $exception->getCode());
        } 
    }


        public function current_user_pay(Request $request): JsonResponse
        {
            
            try {
                return $this->responseSuccess(
                    $this->feescalculationRepository->current_user_pay($request->all()),
                    'User payment fetch successfully.'
                );
            } catch (Exception $exception) {
                return $this->responseError([], $exception->getMessage(), $exception->getCode());
            }
        }


        public function all_user_payments(Request $request): JsonResponse
        {
            
            try {
                return $this->responseSuccess(
                    $this->feescalculationRepository->all_user_pay($request->all()),
                    'User all payment fetch successfully.'
                );
            } catch (Exception $exception) {
                return $this->responseError([], $exception->getMessage(), $exception->getCode());
            }
        }

        
        public function get_payment_detail(Request $request): JsonResponse
        {
            
            try {
                return $this->responseSuccess(
                    $this->feescalculationRepository->get_payment_detail($request->all()),
                    'Payment Dtail fetch successfully.'
                );
            } catch (Exception $exception) {
                return $this->responseError([], $exception->getMessage(), $exception->getCode());
            }
        }
      
        public function invoice_generate(Request $request): JsonResponse
        {
            
            try {
                return $this->responseSuccess(
                    $this->feescalculationRepository->invoice_generate(),
                    'User all payment fetch successfully.'
                );
            } catch (Exception $exception) {
                return $this->responseError([], $exception->getMessage(), $exception->getCode());
            }
        }
      

    
}
