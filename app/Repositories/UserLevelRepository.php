<?php

namespace App\Repositories;

use Exception;
use App\Interfaces\DBPreparableInterface;
use App\Models\UserLevel;
use App\Interfaces\UserLevelInterface ;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UserLevelRepository implements UserLevelInterface, DBPreparableInterface {
    public function getAll(array $filterData)
    {
        $filter = $this->getFilterData($filterData);
        $query = UserLevel::get(); 
        // $query = UserLevel::orderBy($filter['orderBy'], $filter['order']);

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

    public function getById(int $id): ?UserLevel
    {
        $user_level = UserLevel::find($id);

        if (empty($user_level)) {
            throw new Exception("User level does not exist.", Response::HTTP_NOT_FOUND);
        }

        return $user_level;
    }

    public function create(array $data): ?UserLevel
    {
        $data = $this->prepareForDB($data);

        return UserLevel::create($data);
    }

    public function update(int $id, array $data): ?UserLevel
    {
        $user_level = $this->getById($id);

        $updated = $user_level->update($this->prepareForDB($data, $user_level));

        if ($updated) {
            $user_level = $this->getById($id);
        }

        return $user_level;
    }

    public function delete(int $id): ?UserLevel
    {
        $user_level = $this->getById($id);
        $deleted = $user_level->delete();

        if (!$deleted) {
            throw new Exception("User level could not be deleted.", Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $user_level;
    }

    public function prepareForDB(array $data, ?UserLevel $user_level = null): array
    {
        return [
            'level' => $data['level'],
            'title' => $data['title'],
        ];
    }

    




}