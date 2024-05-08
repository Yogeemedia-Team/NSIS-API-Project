<?php

namespace App\Repositories;

use Exception;
use App\Interfaces\DBPreparableInterface;
use App\Interfaces\StudentPromoteInterface ;
use App\Models\StudentDetail;
use App\Models\StudentMonthlyFee;
use App\Models\PromoteHistory;
use App\Models\StudentPromoteHistory;
use Illuminate\Http\Response;


class StudentPromoteRepository implements StudentPromoteInterface {
    public function promote($data): ?object 
    {
        $promoteHistoryId = null;
        $studentPromoteDetails = $data['student_data'];

        $studentPromoteIssue = [];
        $studentPromoteSuccess = [];
       
        foreach($studentPromoteDetails as $key => $studentPromoteDetail){
           
            $studentDetail = StudentDetail::where('student_id',$studentPromoteDetail['student_id'])->first();
            if($studentDetail){

                $studenMonthlyFee = StudentMonthlyFee::where('student_id',$studentDetail->student_id)->where('status',1)->first();
           
                $studentMonthlyFeeData = [
                    'student_id' => $studentPromoteDetail['student_id'], 
                    'sd_year_grade_class_id' => $data['promoted_sd_year_grade_class_id'],
                    'monthly_fee' => $studentPromoteDetail['monthly_fee'], 
                    'start_from' => now()->format('Y-m-d'), 
                    'status' => 1,
                ];
            
                if($studenMonthlyFee){
                    $studenMonthlyFee->end_from = now()->format('Y-m-d');
                    $studenMonthlyFee->status = 0;
                    $studenMonthlyFee->update();

                    $studentMonthlyFeeDetail = StudentMonthlyFee::create($studentMonthlyFeeData);
                    
                }else{
                    $studentMonthlyFeeDetail = StudentMonthlyFee::create($studentMonthlyFeeData);
                }
                
                if($key == 0 ){
                    $promoteHistory = [
                        'prev_sd_year_grade_class_id' => $data['prev_sd_year_grade_class_id'],
                        'promoted_sd_year_grade_class_id' => $data['promoted_sd_year_grade_class_id'],
                        'promoted_start_from' => $studentDetail['sd_promote_started_date'],
                        'promoted_end_from' => now()->format('Y-m-d'),
                    ] ;
                    $promoteHistoryDetail =  PromoteHistory::create($promoteHistory);
                    $promoteHistoryId = $promoteHistoryDetail["id"];
                };

                $studentPromoteHistory = [
                    'student_id' => $studentDetail['student_id'] ,
                    'promote_history_id' => $promoteHistoryId,
                    'monthly_fee' => $studentPromoteDetail['monthly_fee'],
                    'st_monthly_fee_id' => $studentMonthlyFeeDetail["id"]
                ];
            
                StudentPromoteHistory::create($studentPromoteHistory);
            
                $studentDetail->sd_year_grade_class_id = $data['promoted_sd_year_grade_class_id'];
                $studentDetail->sd_promote_started_date = now()->format('Y-m-d');
                $studentDetail->update();
                array_push($studentPromoteSuccess, $studentPromoteDetail );
            }else{
                array_push($studentPromoteIssue, $studentPromoteDetail );
                
            }
           
        };

        $collection = collect([
            'studentPromoteIssuesList' => $studentPromoteIssue,
            'studentPromoteSuccessList' => $studentPromoteSuccess,
        ]);
       
        return $collection;
    }



}