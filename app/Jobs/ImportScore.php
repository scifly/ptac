<?php
namespace App\Jobs;

use Illuminate\Support\Facades\Log;
use Throwable;
use Exception;
use Validator;
use App\Models\Score;
use App\Models\Student;
use App\Events\JobResponse;
use Illuminate\Bus\Queueable;
use App\Helpers\HttpStatusCode;
use Illuminate\Support\Facades\DB;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ImportScore implements ShouldQueue {
    
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    
    protected $data, $classId, $userId;
    
    /**
     * Create a new job instance.
     *
     * @param array $data
     * @param $userId
     * @param $classId
     */
    public function __construct(array $data, $userId, $classId) {
        
        $this->data = $data;
        $this->userId = $userId;
        $this->classId = $classId;
        
    }
    
    /**
     * @return bool
     * @throws Exception
     * @throws Throwable
     */
    public function handle() {
    
        $response = [
            'userId' => $this->userId,
            'title' => __('messages.score.title'),
            'statusCode' => HttpStatusCode::OK,
            'message' => __('messages.score.score_imported')
        ];
        list($inserts, $updates, $illegals) = $this->validateData($this->data);
        Log::debug(empty($inserts));
        Log::debug(empty($updates));
        Log::debug(json_encode($illegals));
        if (empty($updates) && empty($inserts)) {
            # 数据格式不正确，中止任务
            $response['statusCode'] = HttpStatusCode::NOT_ACCEPTABLE;
            $response['message'] = __('messages.invalid_data_format');
        } else {
            try {
                DB::transaction(function () use ($inserts, $updates) {
                    # 插入数据
                    $this->insert($inserts);
                    # 更新数据
                    $this->update($updates);
                });
            } catch (Exception $e) {
                $response['statusCode'] = $e->getCode();
                $response['messages'] = $e->getMessage();
            }
            # todo: 生成非法数据excel文件及下载链接
        }
        event(new JobResponse($response));
    
        return true;
        
    }
    
    /**
     * 验证导入数据的合法性，并返回需要插入/更新/重新导入的数据
     *
     * @param $data
     * @return array
     */
    private function validateData($data) {
    
        $rules = [
            'student_number' => 'required',
            'subject_id'     => 'required|integer',
            'exam_id'        => 'required|integer',
            'score'          => 'required|numeric',
        ];
        # 不合法的数据
        $illegals = [];
        # 更新的数据
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
            $status = Validator::make($score, $rules);
            if ($status->fails()) {
                $illegals[] = $datum;
                continue;
            }
            $student = Student::whereStudentNumber($score['student_number'])->first();
            # 数据非法
            if (!$student) {
                $illegals[] = $datum;
                continue;
            }
            # 判断这个学生是否在这个班级
            if ($student->class_id != $this->classId) {
                $illegals[] = $datum;
                continue;
            }
            $scoreExists = Score::whereEnabled(1)
                ->whereExamId($score['exam_id'])
                ->whereStudentId($student->id)
                ->whereSubjectId($score['subject_id'])
                ->first();
            if ($scoreExists) {
                $updates[] = $score;
            } else {
                $inserts[] = $score;
            }
            unset($score);
        }
        
        return [$inserts, $updates, $illegals];
        
    }
    
    /**
     * 插入导入的考试成绩
     *
     * @param array $inserts
     * @throws Exception
     */
    private function insert(array $inserts) {
    
        try {
            DB::transaction(function () use ($inserts) {
                foreach ($inserts as $insert) {
                    $student = Student::whereStudentNumber($insert['student_number'])->first();
                    #先创建记录
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
        
    }
    
    /**
     * 更新导入的考试成绩
     *
     * @param array $updates
     * @throws Exception
     */
    private function update(array $updates) {
    
        try {
            DB::transaction(function () use ($updates) {
                foreach ($updates as $update) {
                    $student = Student::whereStudentNumber($update['student_number'])->first();
                    #先找到需要更新的记录
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
        
    }
    
}
