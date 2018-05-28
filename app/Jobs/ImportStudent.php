<?php
namespace App\Jobs;

use App\Events\JobResponse;
use App\Helpers\HttpStatusCode;
use App\Helpers\ModelTrait;
use App\Models\Grade;
use App\Models\School;
use App\Models\Squad;
use App\Models\Student;
use App\Rules\Mobiles;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Validator;

class ImportStudent implements ShouldQueue {
    
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, ModelTrait;
    
    protected $students, $userId;
    
    const EXCEL_FILE_TITLE = [
        '姓名', '性别', '生日', '学校',
        '年级', '班级', '手机号码',
        '学号', '卡号', '住校',
        '备注', '监护关系',
    ];
    
    /**
     * Create a new job instance.
     *
     * @param array $students - 学籍Excel文件
     * @param integer $userId
     */
    public function __construct(array $students, $userId) {
        
        $this->students = $students;
        $this->userId = $userId;
        
    }
    
    /**
     * @throws Exception
     * @throws \Throwable
     */
    public function handle() {

        $response = $response = [
            'userId' => $this->userId,
            'title' => '批量导入学籍',
            'statusCode' => HttpStatusCode::OK,
            'message' => __('messages.ok')
        ];
        list($updates, $inserts, $illegals) = $this->validate($this->students);
        Log::debug(json_encode([$updates, $inserts, $illegals]));
        return false;
        if (empty($updates) && empty($inserts)) {
            # 数据格式不正确，中止任务
            $response['statusCode'] = HttpStatusCode::NOT_ACCEPTABLE;
            $response['message'] = __('messages.student.invalid_import_data');
            event(new JobResponse($response));
            return false;
        }
        # 验证导入数据
        $response['statusCode'] = HttpStatusCode::ACCEPTED;
        $response['message'] = sprintf(
            __('messages.student.import_request_submitted'),
            sizeof($inserts), sizeof($updates), sizeof($illegals)
        );
        $response['url'] = ''; # todo: 生成非法数据excel文件及下载地址
        event(new JobResponse($response));
        # 新增数据
        
        # 更新数据
        
        return true;
        
    }
    
    /**
     * 检查每行数据 是否符合导入数据
     *
     * @param array $students
     * @return array
     */
    private function validate(array $students): array {
        
        $rules = [
            'name'           => 'required|string|between:2,6',
            'gender'         => ['required', Rule::in(['男', '女'])],
            'birthday'       => 'required|date',
            'school'         => 'required|string|between:4,20',
            'grade'          => 'required|string|between:3,20',
            'class'          => 'required|string|between:2,20',
            'mobile'         => 'required', new Mobiles(),
            'student_number' => 'required|alphanum|between:2,32',
            'card_number'    => 'required|alphanum|between:2,32',
            'oncampus'       => ['required', Rule::in(['住读', '走读'])],
            'remark'         => 'string|nullable',
            'relationship'   => 'string',
        ];
        # 不合法的数据
        $illegals = [];
        # 更新的数据
        $updates = [];
        # 需要添加的数据
        $inserts = [];
        for ($i = 0; $i < count($students); $i++) {
            $schoolName = $students[$i]['C'];
            $gradeName = $students[$i]['D'];
            $className = $students[$i]['E'];
            $sn = $students[$i][7];
            $user = [
                'name'           => $students[$i]['A'],
                'gender'         => $students[$i]['B'],
                'birthday'       => $students[$i]['C'],
                'school'         => $schoolName,
                'grade'          => $gradeName,
                'class'          => $className,
                'mobile'         => $students[$i]['F'],
                'student_number' => $sn,
                'card_number'    => $students[$i]['H'],
                'oncampus'       => $students[$i]['I'],
                'remark'         => $students[$i]['J'],
                'relationship'   => $students[$i]['K'],
                'class_id'       => 0,
                'department_id'  => 0,
            ];
            $isValid = Validator::make($user, $rules)->fails();
            $school = $isValid ? School::whereName($schoolName)->first() : null;
            $isSchoolValid = $school ? in_array($school->id, $this->schoolIds($this->userId)) : false;
            $grade = $school ? Grade::whereName($gradeName)->where('school_id', $school->id)->first() : null;
            $isGradeValid = $grade ? in_array($grade->id, $this->gradeIds($school->id, $this->userId)) : false;
            $class = $grade ? Squad::whereName($className)->where('grade_id', $grade->id)->first() : null;
            $isClassValid = $class ? in_array($class->id, $this->classIds($school->id, $this->userId)) : false;
            # 数据非法
            if (!($isValid && $isSchoolValid && $isGradeValid && $isClassValid)) {
                $illegals[] = $students[$i];
                continue;
            }
            $student = Student::whereStudentNumber($sn)->where('class_id', $class->id)->first();
            $user['class_id'] = $class->id;
            $user['department_id'] = $class->department_id;
            # 学生数据已存在 更新操作
            if ($student) {
                $updates[] = $user;
            } else {
                $inserts[] = $user;
            }
        }
        
        return [$updates, $inserts, $illegals];
        
    }
    
}
