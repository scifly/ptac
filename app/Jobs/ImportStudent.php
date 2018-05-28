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
    
    protected $filename, $userId;
    
    const EXCEL_FILE_TITLE = [
        '姓名', '性别', '生日', '学校',
        '年级', '班级', '手机号码',
        '学号', '卡号', '住校',
        '备注', '监护关系',
    ];
    
    /**
     * Create a new job instance.
     *
     * @param string $filename - 学籍Excel文件
     * @param integer $userId
     */
    public function __construct($filename, $userId) {
        
        $this->filename = $filename;
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
        $spreadsheet = IOFactory::load(
            $this->uploadedFilePath($this->filename)
        );
        $students = $spreadsheet->getActiveSheet()->toArray(
            null, true, true, true
        );
        if (!empty(array_diff(self::EXCEL_FILE_TITLE, $students[0]))) {
            # 文件格式不正确，中止任务
            $response['statusCode'] = HttpStatusCode::NOT_ACCEPTABLE;
            $response['message'] = __('messages.student.invalid_file_format');
            event(new JobResponse($response));
            return false;
        }
        unset($students[0]);
        $students = array_values($students);
        Log::debug(json_encode($students));
        if (!empty($students)) {
            # 去除表格的空数据
            foreach ($students as $key => $v) {
                if ((array_filter($v)) == null) {
                    unset($students[$key]);
                }
            }
            list($updates, $inserts, $illegals) = $this->validate($students);
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
        }
        # 导入数据为空，中止任务
        $response['statusCode'] = HttpStatusCode::NOT_ACCEPTABLE;
        $response['message'] = __('messages.student.empty_import_file');
        event(new JobResponse($response));
        
        return false;
        
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
            $schoolName = $students[$i][3];
            $gradeName = $students[$i][4];
            $className = $students[$i][5];
            $sn = $students[$i][7];
            $user = [
                'name'           => $students[$i][0],
                'gender'         => $students[$i][1],
                'birthday'       => $students[$i][2],
                'school'         => $schoolName,
                'grade'          => $gradeName,
                'class'          => $className,
                'mobile'         => $students[$i][6],
                'student_number' => $sn,
                'card_number'    => $students[$i][8],
                'oncampus'       => $students[$i][9],
                'remark'         => $students[$i][10],
                'relationship'   => $students[$i][11],
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
