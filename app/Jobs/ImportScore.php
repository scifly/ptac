<?php
namespace App\Jobs;

use App\Helpers\JobTrait;
use App\Helpers\ModelTrait;
use App\Models\Score;
use App\Models\Student;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Throwable;
use Validator;

/**
 * Class ImportScore
 * @package App\Jobs
 */
class ImportScore implements ShouldQueue {
    
    use Dispatchable, InteractsWithQueue, Queueable,
        SerializesModels, ModelTrait, JobTrait;
    
    public $data, $userId, $classId;
    
    /**
     * Create a new job instance.
     *
     * @param array $data
     * @param $userId
     * @param $classId
     */
    function __construct(array $data, $userId, $classId) {
        
        $this->data = $data;
        $this->userId = $userId;
        $this->classId = $classId;
        
    }
    
    /**
     * @return bool
     * @throws Exception
     * @throws Throwable
     */
    function handle() {
        
        return $this->import($this, 'messages.score.title');
        
    }
    
    /**
     * 验证导入数据的合法性，并返回需要插入/更新/重新导入的数据
     *
     * @param $data
     * @return array
     */
    function validate($data) {
        
        $rules = [
            'student_number' => 'required',
            'subject_id'     => 'required|integer',
            'exam_id'        => 'required|integer',
            'score'          => 'required|numeric',
        ];
        # 非法数据
        $illegals = [];
        # 需要更新的数据
        $updates = [];
        # 需要添加的数据
        $inserts = [];
        for ($i = 0; $i < count($data); $i++) {
            $datum = $data[$i];
            $score = [
                'student_number' => $datum['student_number'],
                'subject_id'     => $datum['subject_id'],
                'exam_id'        => $datum['exam_id'],
                'score'          => $datum['score'],
            ];
            $result = Validator::make($score, $rules);
            if ($result->fails()) {
                $datum['error'] = json_encode($result->errors());
                $illegals[] = array_values($datum);
                continue;
            }
            $student = Student::whereStudentNumber($score['student_number'])->first();
            # 数据非法
            if (!$student) {
                $datum['error'] = __('messages.student.not_found');
                $illegals[] = array_values($datum);
                continue;
            }
            # 判断这个学生是否在这个班级
            if ($student->class_id != $this->classId) {
                $datum['error'] = __('messages.score.student_class_mismatch');
                $illegals[] = array_values($datum);
                continue;
            }
            $scoreExists = Score::where([
                'exam_id'    => $score['exam_id'],
                'student_id' => $student->id,
                'subject_id' => $score['subject_id'],
                'enabled'    => 1,
            ])->first();
            if ($scoreExists) {
                $updates[] = $score;
            } else {
                $inserts[] = $score;
            }
        }
        
        return [$inserts, $updates, $illegals];
        
    }
    
    /**
     * 插入导入的考试成绩
     *
     * @param array $inserts
     * @return bool
     * @throws Throwable
     */
    function insert(array $inserts) {
        
        try {
            DB::transaction(function () use ($inserts) {
                foreach ($inserts as $insert) {
                    $student = Student::whereStudentNumber($insert['student_number'])->first();
                    Score::create([
                        'student_id' => $student->id,
                        'subject_id' => $insert['subject_id'],
                        'exam_id'    => $insert['exam_id'],
                        'score'      => $insert['score'],
                        'class_rank' => 0,
                        'grade_rank' => 0,
                        'enabled'    => 1,
                    ]);
                }
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
    /**
     * 更新导入的考试成绩
     *
     * @param array $updates
     * @return bool
     * @throws Throwable
     */
    function update(array $updates) {
        
        try {
            DB::transaction(function () use ($updates) {
                foreach ($updates as $update) {
                    $student = Student::whereStudentNumber($update['student_number'])->first();
                    $score = Score::whereEnabled(1)
                        ->whereExamId($update['exam_id'])
                        ->whereStudentId($student->id)
                        ->whereSubjectId($update['subject_id'])
                        ->first();
                    $score->update([
                        'student_id' => $student->id,
                        'subject_id' => $update['subject_id'],
                        'exam_id'    => $update['exam_id'],
                        'score'      => $update['score'],
                        'class_rank' => 0,
                        'grade_rank' => 0,
                        'enabled'    => 1,
                    ]);
                }
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
}
