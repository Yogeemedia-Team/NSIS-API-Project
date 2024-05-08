<?php

namespace App\Repositories;

use Exception;
use App\Interfaces\DBPreparableInterface;
use App\Models\UserAssigning;
use App\Interfaces\UserAssigneeInterface ;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Http\Response;


class UserAssigneeRepository implements UserAssigneeInterface, DBPreparableInterface {
    public function getAll(array $filterData)
    {
        $filter = $this->getFilterData($filterData);
        $query = UserAssigning::get(); 
        // $query = UserAssigning::orderBy($filter['orderBy'], $filter['order']);

        // if (!empty($filter['search'])) {
        //     $query->where(function ($query) use ($filter) {
        //         $searched = '%' . $filter['search'] . '%';
        //         $query->where('user_id', 'like', $searched)
        //         ->orWhere('role_id', 'like', $searched);
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

    public function getById(int $id): ?UserAssigning
    {
        $assign_permission = UserAssigning::find($id);

        if (empty($assign_permission)) {
            throw new Exception("Assign permission does not exist.", Response::HTTP_NOT_FOUND);
        }

        return $assign_permission;
    }

    public function create(array $data): ?UserAssigning
    {
        $data = $this->prepareForDB($data);

        return UserAssigning::create($data);
    }

    public function update(int $id, array $data): ?UserAssigning
    {
        $assign_permission = $this->getById($id);

        $updated = $assign_permission->update($this->prepareForDB($data, $assign_permission));

        if ($updated) {
            $assign_permission = $this->getById($id);
        }

        return $assign_permission;
    }

    public function delete(int $id): ?UserAssigning
    {
        $assign_permission = $this->getById($id);
        $deleted = $assign_permission->delete();

        if (!$deleted) {
            throw new Exception("Assign permission could not be deleted.", Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $assign_permission;
    }

    public function prepareForDB(array $data, ?UserAssigning $assign_permission = null): array
    {
        return [
            'user_id' => $data['user_id'],
            'level_id' => $data['level_id'],
            'role_id' => $data['role_id'],
        ];
    }

    




}