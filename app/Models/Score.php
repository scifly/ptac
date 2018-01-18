<?php
namespace App\Models;

use App\Events\ScoreImported;
use App\Events\ScoreUpdated;
use App\Facades\DatatableFacade as Datatable;
use Carbon\Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Readers\LaravelExcelReader;

/**
 * App\Models\Score 分数
 *
 * @property int $id
 * @property int $student_id 学生ID
 * @property int $subject_id 科目ID
 * @property int $exam_id 考试ID
 * @property int $class_rank 班级排名
 * @property int $grade_rank 年级排名
 * @property float $score 分数
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $enabled 是否参加考试
 * @method static Builder|Score whereClassRank($value)
 * @method static Builder|Score whereCreatedAt($value)
 * @method static Builder|Score whereEnabled($value)
 * @method static Builder|Score whereExamId($value)
 * @method static Builder|Score whereGradeRank($value)
 * @method static Builder|Score whereId($value)
 * @method static Builder|Score whereScore($value)
 * @method static Builder|Score whereStudentId($value)
 * @method static Builder|Score whereSubjectId($value)
 * @method static Builder|Score whereUpdatedAt($value)
 * @mixin Eloquent
 * @property-read Exam $exam
 * @property-read Student $student
 * @property-read Subject $subject
 */
class Score extends Model {
    
    #表头必须包含字段
    const EXCEL_FILE_TITLE = [
        '班级', '学号', '姓名',
    ];
    
    protected $fillable = [
        'student_id', 'subject_id', 'exam_id',
        'class_rank', 'grade_rank', 'score',
        'enabled',
    ];
    
    /**
     * 返回分数记录所属的学生对象
     *
     * @return BelongsTo
     */
    public function student() { return $this->belongsTo('App\Models\Student'); }
    
    /**
     * 返回分数记录所属的科目对象
     *
     * @return BelongsTo
     */
    public function subject() { return $this->belongsTo('App\Models\Subject'); }
    
    /**
     * 返回分数记录所述的考试对象
     *
     * @return BelongsTo
     */
    public function exam() { return $this->belongsTo('App\Models\Exam'); }
    
    /**
     * 分数记录列表
     *
     * @return array
     */
    static function datatable() {
        
        $columns = [
            ['db' => 'Score.id', 'dt' => 0],
            ['db' => 'User.realname', 'dt' => 1],
            ['db' => 'Grade.name as gradename', 'dt' => 2],
            ['db' => 'Squad.name', 'dt' => 3],
            ['db' => 'Student.student_number', 'dt' => 4],
            ['db' => 'Subject.name as subjectname', 'dt' => 5],
            ['db' => 'Exam.name as examname', 'dt' => 6],
            ['db'        => 'Score.class_rank', 'dt' => 7,
             'formatter' => function ($d) {
                 return $d === 0 ? "未统计" : $d;
             },
            ],
            ['db'        => 'Score.grade_rank', 'dt' => 8,
             'formatter' => function ($d) {
                 return $d === 0 ? "未统计" : $d;
             },
            ],
            ['db' => 'Score.score', 'dt' => 9],
            ['db' => 'Score.created_at', 'dt' => 10],
            ['db' => 'Score.updated_at', 'dt' => 11],
            [
                'db'        => 'Score.enabled', 'dt' => 12,
                'formatter' => function ($d, $row) {
                    return Datatable::dtOps($d, $row);
                },
            ],
        ];
        $joins = [
            [
                'table'      => 'students',
                'alias'      => 'Student',
                'type'       => 'INNER',
                'conditions' => [
                    'Student.id = Score.student_id',
                ],
            ],
            [
                'table'      => 'subjects',
                'alias'      => 'Subject',
                'type'       => 'INNER',
                'conditions' => [
                    'Subject.id = Score.subject_id',
                ],
            ],
            [
                'table'      => 'exams',
                'alias'      => 'Exam',
                'type'       => 'INNER',
                'conditions' => [
                    'Exam.id = Score.exam_id',
                ],
            ],
            [
                'table'      => 'users',
                'alias'      => 'User',
                'type'       => 'INNER',
                'conditions' => [
                    'User.id = Student.user_id',
                ],
            ],
            [
                'table'      => 'classes',
                'alias'      => 'Squad',
                'type'       => 'INNER',
                'conditions' => [
                    'Squad.id = Student.class_id',
                ],
            ],
            [
                'table'      => 'grades',
                'alias'      => 'Grade',
                'type'       => 'INNER',
                'conditions' => [
                    'Grade.id = Squad.grade_id',
                ],
            ],
        ];
        
        // todo: 增加过滤条件
        return Datatable::simple(self::getModel(), $columns, $joins);
        
    }
    
    /**
     * @param $exam_id
     * @return boolean
     */
    static function statistics($exam_id) {
        #取到当前的学校id
        // $schoolId = School::schoolId();
        $exam = Exam::whereId($exam_id)->first();
        #找到考试对应的科目存到数组 ids
        $examSub = explode(',', $exam->subject_ids);
        #找到考试对应的班级存到数组 ids
        $examCla = explode(',', $exam->class_ids);
        #找到班级下面对应所有的学生 ids
        $claStuIds = [];
        foreach ($examCla as $cla) {
            $squad = Squad::whereId($cla)->first();
            foreach ($squad->students as $student) {
                $claStuIds[] = $student->id;
            }
        }
        foreach ($examSub as $sub) {
            #一次处理一个科目  查出这个科目下 班级下所有学生的成绩
            # 若该学生id没有对应的score则不会在结果数组中
            $scores = Score::whereExamId($exam_id)
                ->whereSubjectId($sub)
                ->whereIn('student_id', $claStuIds)
                ->whereEnabled(1)
                ->orderBy('score', 'desc')
                ->get();
            #这个时候key就是排名 处理班级排名
            foreach ($scores as $key => $score) {
                $score->class_rank = $key + 1;
                $score->save();
            }
            #年级排名
            $scoresAll = Score::whereExamId($exam_id)
                ->whereSubjectId($sub)
                ->whereEnabled(1)
                ->orderBy('score', 'desc')
                ->get();
            foreach ($scoresAll as $key => $score) {
                $score->grade_rank = $key + 1;
                $score->save();
            }
            #参加这个科目 这个考试的人数
            // print_r($scores);
        }
        
        return true;
        // $class_ids = DB::table('exams')->where('id', $exam_id)->value('class_ids');
        // $class = DB::table('classes')
        //     ->whereIn('id', explode(',', $class_ids))
        //     ->select('id', 'grade_id')
        //     ->get();
        // //通过年级分组
        // $grades = [];
        // foreach ($class as $item) {
        //     $grades[$item->grade_id][] = $item->id;
        // }
        // //循环每个年级
        // foreach ($grades as $class_ids_arr) {
        //     //查找此年级所有班级的学生的各科成绩
        //     $score = self::join('students', 'students.id', '=', 'scores.student_id')
        //         ->whereIn('students.class_id', $class_ids_arr)
        //         ->where('scores.exam_id', $exam_id)
        //         ->select(['scores.id', 'scores.student_id', 'scores.subject_id', 'scores.score', 'students.class_id'])
        //         ->orderBy('scores.score', 'desc')
        //         ->get();
        //     //通过科目分组
        //     $subject = [];
        //     foreach ($score as $item) {
        //         $subject[$item->subject_id][] = $item;
        //     }
        //     //循环每个科目
        //     foreach ($subject as $val) {
        //         foreach ($val as $k => $v) {
        //             $v->grade_rank = $k + 1;
        //             if ($k > 0) {
        //                 if ($v->score == $val[$k - 1]->score) {
        //                     $v->grade_rank = $val[$k - 1]->grade_rank;
        //                 }
        //             }
        //         }
        //         //写入年级排名
        //         foreach ($val as $grade_rank) {
        //             self::find($grade_rank->id)->update(['grade_rank' => $grade_rank->grade_rank]);
        //         }
        //         //通过班级分组
        //         $classes = [];
        //         foreach ($val as $item) {
        //             $classes[$item->class_id][] = $item;
        //         }
        //         //循环每个班级
        //         foreach ($classes as $v) {
        //             foreach ($v as $class_k => $class_v) {
        //                 $class_v->class_rank = $class_k + 1;
        //                 if ($class_k > 0) {
        //                     if ($class_v->score == $v[$class_k - 1]->score) {
        //                         $class_v->class_rank = $v[$class_k - 1]->class_rank;
        //                     }
        //                 }
        //             }
        //             //写入年级排名
        //             foreach ($v as $class_rank) {
        //                 self::find($class_rank->id)->update(['class_rank' => $class_rank->class_rank]);
        //             }
        //         }
        //     }
        // }
        //
    }
    
    /**
     * @param UploadedFile $file
     * @param $input
     * @return array
     * @throws \PHPExcel_Exception
     */
    static function upload(UploadedFile $file, $input) {
        
        $ext = $file->getClientOriginalExtension();     // 扩展名//xls
        $realPath = $file->getRealPath();   //临时文件的绝对路径
        // 上传文件
        $filename = date('His') . uniqid() . '.' . $ext;
        $stored = Storage::disk('uploads')->put($filename, file_get_contents($realPath));
        if ($stored) {
            $filePath =
                'uploads/'
                . date('Y')
                . '/'
                . date('m')
                . '/'
                . date('d')
                . '/'
                . $filename;
            /** @var LaravelExcelReader $reader */
            $reader = Excel::load($filePath);
            $sheet = $reader->getExcel()->getSheet(0);
            $scores = $sheet->toArray();
            #考虑删除读取过后的xml文件
            if (is_file($filePath)) {
                unlink($filePath);
            }
            if (self::checkFileFormat($scores[0])) {
                return [
                    'error'   => 1,
                    'message' => '文件格式错误',
                ];
            }
            $schoolId = School::schoolId();
            #这次考试对应的科目id
            $exam = Exam::whereId($input['exam_id'])->first();
            $subjectIds = explode(',', $exam->subject_ids);
            $scoreArr = $scores;
            #去除表头后的数据
            array_shift($scoreArr);
            $scoreArr = array_values($scoreArr);
            if (count($scoreArr) != 0) {
                # 去除表格的空数据
                foreach ($scoreArr as $key => $v) {
                    if ((array_filter($v)) == null) {
                        unset($scoreArr[$key]);
                    }
                }
            }
            #处理表头循环单列的数据插入分数
            for ($i = 3; $i < count($scores[0]); $i++) {
                $data = [];
                $sub = $scores[0][$i];
                $subject = Subject::where('school_id', $schoolId)
                    ->where('name', $sub)->first();
                #判断录入科目分数是否在这次考试中  在
                if (in_array($subject->id, $subjectIds)) {
                    foreach ($scoreArr as $arr) {
                        $data[] = [
                            'class'          => $arr[0],
                            'student_number' => $arr[1],
                            'student_name'   => $arr[2],
                            'subject_id'     => $subject->id,
                            'score'          => $arr[$i],
                            'exam_id'        => $input['exam_id'],
                        ];
                        
                    }
                    if (!self::checkData($data, $input)) {
                        return ['statusCode' => 500, 'message' => '数据有误!',];
                    }
                } else {
                    unset($sub);
                }
            }
            unset($scores);
            unset($scoreArr);
            
            return ['statusCode' => 200, 'message' => '上传成功'];
        }
        
        return ['statusCode' => 500, 'message' => '上传失败'];
    }
    
    /**
     * 检查表头是否合法
     * @param array $fileTitle
     * @return bool
     */
    private static function checkFileFormat(array $fileTitle) {
        
        return count(array_diff(self::EXCEL_FILE_TITLE, $fileTitle)) != 0;
        
    }
    
    /**
     *  检查每行数据 是否符合导入数据
     * @param array $data
     * @return bool
     */
    private static function checkData(array $data, $input) {
        $rules = [
            'student_number' => 'required',
            'subject_id'     => 'required|integer',
            'exam_id'        => 'required|integer',
            'score'          => 'required|numeric',
        ];
        # 不合法的数据
        $invalidRows = [];
        # 更新的数据
        $updateRows = [];
        # 需要添加的数据
        $rows = [];
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
                $invalidRows[] = $datum;
                unset($data[$i]);
                continue;
            }
            $student = Student::whereStudentNumber($score['student_number'])->first();
            # 数据非法
            if (!$student) {
                $invalidRows[] = $datum;
                unset($data[$i]);
                continue;
            }
            #判断这个学生是否在这个班级
            if ($student->class_id != $input['class_id']) {
                $invalidRows[] = $datum;
                unset($data[$i]);
                continue;
            }
            $existScore = Score::whereEnabled(1)
                ->whereExamId($score['exam_id'])
                ->whereStudentId($student->id)
                ->whereSubjectId($score['subject_id'])
                ->first();
            if ($existScore) {
                $updateRows[] = $score;
            } else {
                $rows[] = $score;
            }
            unset($score);
        }
        event(new ScoreUpdated($updateRows));
        event(new ScoreImported($rows));
        if (empty($rows) && empty($updateRows)) {
            return false;
        }
        
        return true;
    }
}
