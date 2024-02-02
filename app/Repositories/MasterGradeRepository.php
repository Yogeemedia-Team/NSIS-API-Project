<?php

namespace App\Repositories;

use Exception;
use App\Interfaces\DBPreparableInterface;
use App\Models\MasterGrade;
use App\Interfaces\MasterGradeInterface ;
use Illuminate\Http\Response;


class MasterGradeRepository implements MasterGradeInterface, DBPreparableInterface {
    public function getAll(array $filterData)
    {
        $filter = $this->getFilterData($filterData);
        $query = MasterGrade::get(); 
        // $query = MasterGrade::orderBy($filter['orderBy'], $filter['order']);

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

    public function getById(int $id): ?MasterGrade
    {
        $master_grade = MasterGrade::find($id);

        if (empty($master_grade)) {
            throw new Exception("Master grade does not exist.", Response::HTTP_NOT_FOUND);
        }

        return $master_grade;
    }

    public function create(array $data): ?MasterGrade
    {
        $data = $this->prepareForDB($data);

        return MasterGrade::create($data);
    }

    public function update(array $data, int $id): ?MasterGrade
    {
        $master_grade = $this->getById($id);

        $updated = $master_grade->update($this->prepareForDB($data, $master_grade));

        if ($updated) {
            $master_grade = $this->getById($id);
        }

        return $master_grade;
    }

    public function delete(int $id): ?MasterGrade
    {
        $master_grade = $this->getById($id);
        $deleted = $master_grade->delete();

        if (!$deleted) {
            throw new Exception("Master grade could not be deleted.", Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $master_grade;
    }

    public function prepareForDB(array $data, ?MasterGrade $master_grade = null): array
    {
        return [
            'organization_id' => $data['organization_id'],
            'grade_name' => $data['grade_name'],

        ];
    }

    




}