<?php

namespace App\Repositories;

use Exception;
use App\Interfaces\DBPreparableInterface;
use App\Models\MasterClass;
use App\Interfaces\MasterClassInterface ;
use Illuminate\Http\Response;


class MasterClassRepository implements MasterClassInterface, DBPreparableInterface {
    public function getAll(array $filterData)
    {
        $filter = $this->getFilterData($filterData);
        $query = MasterClass::get(); 
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

    public function getById(int $id): ?MasterClass
    {
        $master_class = MasterClass::find($id);

        if (empty($master_class)) {
            throw new Exception("Master class does not exist.", Response::HTTP_NOT_FOUND);
        }

        return $master_class;
    }

    public function create(array $data): ?MasterClass
    {
        $data = $this->prepareForDB($data);

        return MasterClass::create($data);
    }

    public function update(int $id, array $data): ?MasterClass
    {
        $master_class = $this->getById($id);

        $updated = $master_class->update($this->prepareForDB($data, $master_class));

        if ($updated) {
            $master_class = $this->getById($id);
        }

        return $master_class;
    }

    public function delete(int $id): ?MasterClass
    {
        $master_class = $this->getById($id);
        $deleted = $master_class->delete();

        if (!$deleted) {
            throw new Exception("Master class could not be deleted.", Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $master_class;
    }

    public function prepareForDB(array $data, ?MasterClass $master_class = null): array
    {
        return [
            'organization_id' => $data['organization_id'],
            'class_name' => $data['class_name'],

        ];
    }

    




}