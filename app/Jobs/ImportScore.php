<?php
namespace App\Jobs;

use App\Events\JobResponse;
use App\Helpers\HttpStatusCode;
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
    
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, ModelTrait, JobTrait;
    
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
        // $response = [
        //     'userId'     => $this->userId,
        //     'title'      => __('messages.score.title'),
        //     'statusCode' => HttpStatusCode::OK,
        //     'message'    => __('messages.import_succeeded'),
        // ];
        // list($inserts, $updates, $illegals) = $this->validate($this->data);
        // if (empty($updates) && empty($inserts)) {
        //     # 数据格式不正确，中止任务
        //     $response['statusCode'] = HttpStatusCode::NOT_ACCEPTABLE;
        //     $response['message'] = __('messages.invalid_data_format');
        // } else {
        //     try {
        //         DB::transaction(function () use ($inserts, $updates, $illegals) {
        //             event(new JobResponse([
        //                 'userId' => $this->userId,
        //                 'title' => __('messages.score.title'),
        //                 'statusCode' => HttpStatusCode::ACCEPTED,
        //                 'message' => !count($illegals)
        //                     ? sprintf(
        //                         __('messages.import_request_submitted'),
        //                         count($inserts), count($updates)
        //                     )
        //                     : sprintf(
        //                         __('messages.import_request_submitted') .
        //                         __('messages.import_illegals'),
        //                         count($inserts), count($updates), count($illegals)
        //                     )
        //             ]));
        //             # 插入数据
        //             $this->insert($inserts);
        //             # 更新数据
        //             $this->update($updates);
        //             # 生成错误数据excel文件
        //             if (!empty($illegals)) {
        //                 $this->excel($illegals, 'illegals', '错误数据', false);
        //                 $response['url'] = 'uploads/' . date('Y/m/d/') . 'illegals.xlsx';
        //             }
        //         });
        //     } catch (Exception $e) {
        //         $response['statusCode'] = $e->getCode();
        //         $response['message'] = $e->getMessage();
        //     }
        // }
        // event(new JobResponse($response));
        //
        // return true;
        
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
        }
        
        return [$inserts, $updates, $illegals];
        
    }
    
    /**
     * 插入导入的考试成绩
     *
     * @param array $inserts
     * @throws Exception
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
        
    }
    
    /**
     * 更新导入的考试成绩
     *
     * @param array $updates
     * @throws Exception
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
        
    }
    
}
