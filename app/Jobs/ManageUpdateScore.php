<?php
namespace App\Jobs;

use App\Models\Score;
use App\Models\Student;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ManageUpdateScore implements ShouldQueue {
    
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    
    protected $data;
    
    /**
     * Create a new job instance.
     *
     * @param $data
     */
    public function __construct($data) {
        $this->data = $data;
    }
    
    /**
     * @return bool
     * @throws Exception
     * @throws \Throwable
     */
    public function handle() {
        
        $rows = $this->data;
        #rows 多批次进来
        Log::info($rows);

        try {
            DB::transaction(function () use ($rows) {
                foreach ($rows as $row) {
                    
                    $student = Student::whereStudentNumber($row['student_number'])->first();
                    #先找到需要更新的记录
                    $score = Score::whereEnabled(1)
                        ->whereExamId($row['exam_id'])
                        ->whereStudentId($student->id)
                        ->whereSubjectId($row['subject_id'])
                        ->first();
                    
                    #要更新的数据
                    $scoreData = [
                        'student_id' => $student->id,
                        'subject_id' => $row['subject_id'],
                        'exam_id'    => $row['exam_id'],
                        'score'      => $row['score'],
                        'class_rank' => 0,
                        'grade_rank' => 0,
                        'enabled'    => 1,
                    ];
                    $res = $score->update($scoreData);
                    Log::debug($res);
                }
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
}
