<?php

namespace App\Repositories;

use Exception;
use App\Interfaces\DBPreparableInterface;
use App\Models\UserActivity;
use App\Interfaces\UserActivityInterface;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

class UserActivityRepository implements UserActivityInterface, DBPreparableInterface {
    public function getAll(array $filterData)
    {
        $filter = $this->getFilterData($filterData);

        $query = UserActivity::get();

        // if (!empty($filter['search'])) {
        //     $query->where(function ($query) use ($filter) {
        //         $searched = '%' . $filter['search'] . '%';
        //         $query->where('activity', 'like', $searched);
        //     });
        // }

        return $query;

        
        //return  $permissions = UserActivity::get();
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

    public function getById(int $id): ?UserActivity
    {
        $activity = UserActivity::find($id);

        if (empty($activity)) {
            throw new Exception("User activity does not exist.", Response::HTTP_NOT_FOUND);
        }

        return $activity;
    }

    public function create(array $data): ?UserActivity
    {
        $data = $this->prepareForDB($data);

        return UserActivity::create($data);
    }

    public function update(int $id, array $data): ?UserActivity
    {
        $activity = $this->getById($id);

        $updated = $activity->update($this->prepareForDB($data, $activity));

        if ($updated) {
            $activity = $this->getById($id);
        }

        return $activity;
    }

    public function delete(int $id): ?UserActivity
    {
        $activity = $this->getById($id);
        $deleted = $activity->delete();

        if (!$deleted) {
            throw new Exception("User activity could not be deleted.", Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $activity;
    }

    public function prepareForDB(array $data, ?UserActivity $activity = null): array
    {
        if (empty($data['activity'])) {
            $data['activity'] = $this->createUniqueSlug($data['activity']);
        }
        return $data;
    }

    private function createUniqueSlug(string $title): string
    {
        return Str::slug(substr($title, 0, 80)) . '-' . time();
    }




}