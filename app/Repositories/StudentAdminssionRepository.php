<?php

namespace App\Repositories;

use Exception;
use App\Interfaces\StudentAdminssionInterface ;
use App\Models\StudentAdmission;
use App\Models\AdmissionInstalment;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StudentAdminssionRepository implements StudentAdminssionInterface {

    public function getAll(array $request){

        $query = StudentAdmission::
        leftJoin(DB::raw('(SELECT admission_table_id, COUNT(*) as pending_instalment FROM admission_instalments WHERE status = 0 GROUP BY admission_table_id) as instalments'), function($join) {
                $join->on('instalments.admission_table_id', '=', 'student_admissions.id');
            })
            ->select('student_admissions.*', 'instalments.pending_instalment');
        if(isset($request['admission_no']) && $request['admission_no']){
            $query->where('admission_no', $request['admission_no']);
        }
       $result =  $query->get();
        
        return $result; 
    }

    public function getById(int $id): ? StudentAdmission
    {
        $account_payable = StudentAdmission::with('admissionInstalments')->find($id);

        if (empty($account_payable)) {
            throw new Exception("Payable account does not exist.", Response::HTTP_NOT_FOUND);
        }

        return $account_payable;
    }

    
    public function store(array $data)
    {
      
        
        try {
            // Start a database transaction
            DB::beginTransaction();
            $studentAdmission = StudentAdmission::create([
                'admission_no' => $data['admission_no'], 
                'total_amount' => $data['total_amount'],
                'no_of_instalments' => $data['no_of_instalments'], 
                'status' => 0,
            ]);

            $isAllpaymentDone = false; 
            $admissionInstalmentList = [];
            foreach ($data['admission_instalments'] as $key => $value) {
                $admissionInstalment = AdmissionInstalment::create([
                    'admission_table_id' => $studentAdmission->id, 
                    'admission_no' => $data['admission_no'], 
                    'instalment_amount' => $value['instalment_amount'], 
                    'instalments_no' => $value['instalments_no'], 
                    'reference_no' => isset($value['reference_no']) ? $value['reference_no'] : "",
                    'paid_date' =>$value['status'] == 1 ? now()->format('Y-m-d') : null,
                    'due_date' => $value['due_date'], 
                    'status' => $value['status'],
                ]);

                if($value['status'] == 1){
                    $isAllpaymentDone = true;
                }
                array_push($admissionInstalmentList, $admissionInstalment);
            }

            if($isAllpaymentDone){
                $studentAdmission->status = 1;
                $studentAdmission->save();
            }
            $studentAdmission['admissionInstalments'] = $admissionInstalmentList;
            DB::commit();

            return $data;
            // return "All records inserted successfully!";
        } catch (\Exception $e) {
            // An error occurred, rollback the transaction
            DB::rollBack();

            // Log the exception
            Log::error("Error: " . $e->getMessage());

            // Return an error response
            
            throw new Exception( $e->getMessage(), Response::HTTP_NOT_FOUND);
        }

    }

    
    public function update(array $data)
    {
      
        
        try {
            // Start a database transaction
            DB::beginTransaction();

            $admissioninstalment = AdmissionInstalment::where('id', $data['id'])->first();

            if ($admissioninstalment) {
                $admissioninstalment->update([
                    'paid_date' => $data['paid_date'],
                    'status' => 1,
                    'reference_no' => isset($data['reference_no']) ? $data['reference_no'] : "",
                ]);

                $studentAdmissionInstalments = AdmissionInstalment::where('admission_table_id', $admissioninstalment->admission_table_id)
                    ->where('status', 0)
                    ->get();

                if (count($studentAdmissionInstalments) == 0) {
                    $studentAdmission = StudentAdmission::where('id', $admissioninstalment->admission_table_id)->update([
                        'status' => 1,
                    ]);
                }
            }

            DB::commit();

            return $data;
            // return "All records inserted successfully!";
        } catch (\Exception $e) {
            // An error occurred, rollback the transaction
            DB::rollBack();

            // Log the exception
            Log::error("Error: " . $e->getMessage());

            // Return an error response
            
            throw new Exception( $e->getMessage(), Response::HTTP_NOT_FOUND);
        }

    }

    
    

}