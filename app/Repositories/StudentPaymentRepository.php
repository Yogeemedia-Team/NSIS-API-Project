<?php

namespace App\Repositories;

use Exception;
use App\Interfaces\DBPreparableInterface;
use App\Models\AccountPayable;
use App\Models\StudentPayment;
use App\Interfaces\StudentPaymentInterface ;
use App\Models\StudentDetail;
use Illuminate\Http\Response;


class StudentPaymentRepository implements StudentPaymentInterface, DBPreparableInterface {
    public function getAll(array $filterData)
    {
        $filter = $this->getFilterData($filterData);
        $query = AccountPayable::get(); 
        // $query = MasterClass::orderBy($filter['orderBy'], $filter['order']);

        // if (!empty($filter['search'])) {
        //     $query->where(function ($query) use ($filter) {
        //         $searched = '%' . $filter['search'] . '%';
        //         $query->where('level', 'like', $searched)
        //         ->orWhere('title', 'like', $searched);
        //     });
        // }
        return $query;
    }

    public function getFilterData(array $filterData): array
    {
        $defaultArgs = [
            'perPage' => 10,
            'search' => '',
            'orderBy' => 'id',
            'order' => 'desc'
        ];

        return array_merge($defaultArgs, $filterData);
    }

    public function getById(int $id): ?AccountPayable
    {
        $account_payable = AccountPayable::find($id);

        if (empty($account_payable)) {
            throw new Exception("Payable account does not exist.", Response::HTTP_NOT_FOUND);
        }

        return $account_payable;
    }



    public function update(int $id, array $data): ?AccountPayable
    {
        $account_payable = $this->getById($id);

        $updated = $account_payable->update($this->prepareForDB($data, $account_payable));

        if ($updated) {
            $account_payable = $this->getById($id);
        }

        return $account_payable;
    }

    public function delete(int $id): ?AccountPayable
    {
        $account_payable = $this->getById($id);
        $deleted = $account_payable->delete();

        if (!$deleted) {
            throw new Exception("Payable account could not be deleted.", Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $account_payable;
    }

    public function prepareForDB(array $data, ?AccountPayable $account_payable = null): array
    {
        return [
            'organization_id' => $data['organization_id'],
            'class_name' => $data['class_name'],

        ];
    }

    




}