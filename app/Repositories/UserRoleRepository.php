<?php

namespace App\Repositories;

use Exception;
use App\Interfaces\DBPreparableInterface;
use App\Models\UserRole;
use App\Interfaces\UserRoleInterface ;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UserRoleRepository implements UserRoleInterface, DBPreparableInterface {
    public function getAll(array $filterData)
    {
        $filter = $this->getFilterData($filterData);
        $query = UserRole::get(); 
        // $query = UserRole::orderBy($filter['orderBy'], $filter['order']);

        // if (!empty($filter['search'])) {
        //     $query->where(function ($query) use ($filter) {
        //         $searched = '%' . $filter['search'] . '%';
        //         $query->where('role', 'like', $searched);
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

    public function getById(int $id): ?UserRole
    {
        $user_role = UserRole::find($id);

        if (empty($user_role)) {
            throw new Exception("User role does not exist.", Response::HTTP_NOT_FOUND);
        }

        return $user_role;
    }

    public function create(array $data): ?UserRole
    {
        $data = $this->prepareForDB($data);

        return UserRole::create($data);
    }

    public function update(int $id, array $data): ?UserRole
    {
        $user_role = $this->getById($id);

        $updated = $user_role->update($this->prepareForDB($data, $user_role));

        if ($updated) {
            $user_role = $this->getById($id);
        }

        return $user_role;
    }

    public function delete(int $id): ?UserRole
    {
        $user_role = $this->getById($id);
        $deleted = $user_role->delete();

        if (!$deleted) {
            throw new Exception("User role could not be deleted.", Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $user_role;
    }

    public function prepareForDB(array $data, ?UserRole $user_role = null): array
    {
        return [
            'role' => $data['role'],
        ];
    }

    




}