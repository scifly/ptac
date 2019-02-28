<?php
namespace App\Jobs;

use App\Apis\MassImport;
use App\Helpers\{Constant, HttpStatusCode, JobTrait, ModelTrait};
use App\Models\{Score, Student};
use Exception;
use Illuminate\{Bus\Queueable,
    Contracts\Queue\ShouldQueue,
    Foundation\Bus\Dispatchable,
    Queue\InteractsWithQueue,
    Queue\SerializesModels,
    Support\Facades\DB};
use Pusher\PusherException;
use Throwable;
use Validator;

/**
 * Class ImportScore
 * @package App\Jobs
 */
class ImportScore implements ShouldQueue, MassImport {
    
    use Dispatchable, InteractsWithQueue, Queueable,
        SerializesModels, ModelTrait, JobTrait;
    
    public $data, $userId, $classId, $response;
    const CONDITION_FIELDS = ['student_id', 'subject_id', 'exam_id', 'enabled'];
    
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
        $this->response = array_combine(Constant::BROADCAST_FIELDS, [
            $userId, __('messages.score.title'),
            HttpStatusCode::OK, __('messages.score.import_completed'),
        ]);
        
    }
    
    /**
     * @return bool
     * @throws Exception
     * @throws Throwable
     */
    function handle() {
        
        return $this->import($this, $this->response);
        
    }
    
    /**
     * @param Exception $exception
     * @throws PusherException
     */
    function failed(Exception $exception) {
        
        $this->eHandler($exception, $this->response);
        
    }
    
    /**
     * 验证导入数据的合法性，并返回需要插入/更新/重新导入的数据
     *
     * @param $data
     * @return array
     */
    function validate(array $data) {
        
        $fields = ['sn', 'subject_id', 'exam_id', 'score'];
        $rules = array_combine($fields, [
            'required', 'required|integer',
            'required|integer', 'required|numeric'
        ]);
        for ($i = 0; $i < count($data); $i++) {
            $datum = $data[$i];
            $score = array_combine($fields, [
                $datum['sn'], $datum['subject_id'],
                $datum['exam_id'], $datum['score']
            ]);
            $result = Validator::make($score, $rules);
            if ($result->fails()) {
                $datum['error'] = json_encode($result->errors(), JSON_UNESCAPED_UNICODE);
                $illegals[] = array_values($datum);
                continue;
            }
            $student = Student::whereSn($score['sn'])->first();
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
            $condition = array_combine(self::CONDITION_FIELDS, [
                $student->id, $score['subject_id'], $score['exam_id'], 1
            ]);
            Score::where($condition)->first()
                ? $updates[] = $score
                : $inserts[] = $score;
        }
        
        return [$inserts ?? [], $updates ?? [], $illegals ?? []];
        
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
                    $student = Student::whereSn($insert['sn'])->first();
                    if (!$student) continue;
                    Score::create(
                        array_combine(Constant::SCORE_FIELDS, [
                            $student->id, $insert['subject_id'],
                            $insert['exam_id'], 0, 0, $insert['score'], 1
                        ])
                    );
                }
            });
        } catch (Exception $e) {
            $this->eHandler($e, $this->response);
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
                    $student = Student::whereSn($update['sn'])->first();
                    if (!$student) continue;
                    $condition = array_combine(self::CONDITION_FIELDS, [
                        $student->id, $update['subject_id'], $update['exam_id'], 1
                    ]);
                    Score::where($condition)->first()->update(
                        array_combine(Constant::SCORE_FIELDS, [
                            $student->id, $update['subject_id'],
                            $update['exam_id'], 0, 0, $update['score'], 1
                        ])
                    );
                }
            });
        } catch (Exception $e) {
            $this->eHandler($e, $this->response);
            throw $e;
        }
        
        return true;
        
    }
    
}