<?php

namespace App\Repositories;

use Exception;
use App\Interfaces\DBPreparableInterface;
use App\Interfaces\EnrollmentInterface;
use App\Models\Enrollment;

use Illuminate\Http\Response;
use Illuminate\Support\Str;

class UserEnrollmentRepository implements EnrollmentInterface {
    public function getAll(array $filterData)
    {
        $filter = $this->getFilterData($filterData);

        return  $permissions = Enrollment::get();

        
        //return  $permissions = StudentDetail::get();
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

    public function getById($id): ?Enrollment
    {
       $student = Enrollment::where('admission_id', $id)->first();

        if (empty($student)) {
            throw new Exception("User student does not exist.", Response::HTTP_NOT_FOUND);
        }

        return $student;
    }

    public function create(array $data): ?object 
    {
        $collection = Enrollment::create($data);
        return $collection;
    }

    public function update(array $data, $studentId): ?object 
    {
        // Fetch existing records
        $studentDetails = Enrollment::where('admission_id',$studentId)->first();

        // Check if any of the models is null
        if ($studentDetails === null ) {
            return null;
        }

        // Update the existing records with the new data
        $studentDetails->update($data);

        // Fetch the updated records (optional, depending on your needs)
        $studentDetail = Enrollment::find($studentDetails->id);
        return $studentDetail;
    }



     public function delete($id): ?Enrollment
        {
            $student = Enrollment::where('student_id', $id)->first();
        
            if (!$student) {
                throw new Exception("User student could not be found.", Response::HTTP_NOT_FOUND);
            }
        
            // Update the student's academic status
            $updateResult = Enrollment::where('id', $student->id)->update(['status' => 0]);
        
            if ($updateResult === false) {
                throw new Exception("Failed to delete enrollment user .", Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        
            // Return the deleted student instance
            return $student;
        }

        
    




}