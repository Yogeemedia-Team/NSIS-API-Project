<?php

namespace App\Repositories;

use Exception;
use App\Interfaces\DBPreparableInterface;
use App\Models\YearGradeClass;
use App\Interfaces\YearGradeClassInterface ;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;


class YearGradeClassRepository implements YearGradeClassInterface, DBPreparableInterface {
    public function getAll(array $filterData)
    {
        $filter = $this->getFilterData($filterData);
        // $query = YearGradeClass::with('grade', 'class')
        // // ->selectSub('SELECT COUNT(*) FROM student_service.student_details WHERE student_service.student_details.sd_year_grade_class_id = core_service.year_grade_classes.id', 'student_count')
        // ->get(); 
    
        // $query = YearGradeClass::orderBy($filter['orderBy'], $filter['order']);

        // if (!empty($filter['search'])) {
        //     $query->where(function ($query) use ($filter) {
        //         $searched = '%' . $filter['search'] . '%';
        //         $query->where('level', 'like', $searched)
        //         ->orWhere('title', 'like', $searched);
        //     });
        // }
        $results = YearGradeClass::leftJoin(DB::raw('(SELECT sd_year_grade_class_id, COUNT(*) as student_count FROM student_service.student_details GROUP BY sd_year_grade_class_id) as students'), function($join) {
            $join->on('students.sd_year_grade_class_id', '=', 'core_service.year_grade_classes.id');
        })
        ->with('grade', 'class')
        ->select('core_service.year_grade_classes.*', 'students.student_count')
        ->get();
        return $results;
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

    public function getById(int $id): ?YearGradeClass
    {
        $year_grade_class = YearGradeClass::find($id);

        if (empty($year_grade_class)) {
            throw new Exception("Year grade class relation does not exist.", Response::HTTP_NOT_FOUND);
        }

        return $year_grade_class;
    }

    public function create(array $data): ?YearGradeClass
    {
        $data = $this->prepareForDB($data);

        return YearGradeClass::create($data);
    }

    public function update(int $id, array $data): ?YearGradeClass
    {
        $year_grade_class = $this->getById($id);

        $updated = $year_grade_class->update($this->prepareForDB($data, $year_grade_class));

        if ($updated) {
            $year_grade_class = $this->getById($id);
        }

        return $year_grade_class;
    }

    public function delete(int $id): ?YearGradeClass
    {
        $year_grade_class = $this->getById($id);
        $deleted = $year_grade_class->delete();

        if (!$deleted) {
            throw new Exception("Year grade class relation could not be deleted.", Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $year_grade_class;
    }

    public function prepareForDB(array $data, ?YearGradeClass $year_grade_class = null): array
    {
        return [
            'year' => $data['year'],
            'organization_id' => $data['organization_id'],
            'master_grade_id' => $data['master_grade_id'],
            'master_class_id' => $data['master_class_id'],
            'monthly_fee'     => $data['monthly_fee'],
            'active_status' => $data['active_status'],

        ];
    }

    




}