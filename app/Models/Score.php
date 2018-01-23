<?php
namespace App\Models;

use App\Events\ScoreImported;
use App\Events\ScoreUpdated;
use App\Facades\DatatableFacade as Datatable;
use App\Facades\Wechat;
use Carbon\Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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
     * 排名统计
     *
     * @param $exam_id
     * @return boolean
     */
    static function statistics($exam_id) {
        $exam = Exam::whereId($exam_id)->first();
        #找到考试对应的科目存到数组 ids
        $examSub = explode(',', $exam->subject_ids);
        #找到考试对应的班级存到数组 ids
        $examCla = explode(',', $exam->class_ids);
        #找到每个班级下面对应的学生 ids
        $claStuIds = [];
        foreach ($examCla as $cla) {
            $squad = Squad::whereId($cla)->first();
            foreach ($squad->students as $student) {
                $claStuIds[$squad->id][] = $student->id;
            }
        }
        #根据班级找出参与考试的所有年级
        $grades = [];
        foreach ($examCla as $cla) {
            $grades[] = Squad::whereId($cla)->first()->grade_id;
        }
        #找到每个年级下面的所有学生
        $graStuIds = [];
        foreach (array_unique($grades) as $grade) {
            $squads = Grade::whereId($grade)->first()->classes;
            foreach ($squads as $squad) {
                foreach ($squad->students as $student) {
                    $graStuIds[$grade][] = $student->id;
                }
            }
        }
        foreach ($examSub as $sub) {
            #一次处理一个科目  查出这个科目下 班级下所有学生的成绩
            foreach ($claStuIds as $claStuId) {
                # 若该学生id没有对应的score则不会在结果数组中
                $scores = Score::whereExamId($exam_id)
                    ->whereSubjectId($sub)
                    ->whereIn('student_id', $claStuId)
                    ->whereEnabled(1)
                    ->orderBy('score', 'desc')
                    ->get();
                #比较分数的临时变量 和排名值
                $tempScore = '';
                $rank = 0;
                foreach ($scores as $key => $score) {
                    #若两次分数不相等
                    if ($tempScore != $score->score) {
                        $tempScore = $score->score;
                        $rank += 1;
                    }
                    $score->class_rank = $rank;
                    if (!$score->save()) {
                        return false;
                    }
                }
            }
            #年级排名
            foreach ($graStuIds as $graStuId) {
                $scoresAll = Score::whereExamId($exam_id)
                    ->whereSubjectId($sub)
                    ->whereIn('student_id', $graStuId)
                    ->whereEnabled(1)
                    ->orderBy('score', 'desc')
                    ->get();
                $gradeScore = '';
                $gradeRank = 0;
                foreach ($scoresAll as $key => $score) {
                    if ($gradeScore != $score->score) {
                        $gradeScore = $score->score;
                        $gradeRank += 1;
                    }
                    $score->grade_rank = $gradeRank;
                    if (!$score->save()) {
                        return false;
                    }
                }
            }
        }
        #总分统计
        #按学生id对本次考试成绩 分组
        $studentScore = Score::whereExamId($exam_id)
            ->whereEnabled(1)
            ->get()
            ->groupBy('student_id');
        foreach ($studentScore as $s) {
            #当前学生参加的考试科目数组
            $studentSub = [];
            $studentId = $s[0]->student_id;
            $total = $s->sum('score');
            foreach ($s as $sub) {
                $studentSub[] = $sub->subject_id;
            }
            #没有参加的考试科目
            $noSub = array_diff($examSub, $studentSub);
            $scoreTotalData = [
                'student_id'     => $studentId,
                'exam_id'        => $exam_id,
                'score'          => $total,
                'subject_ids'    => implode(',', $studentSub),
                'na_subject_ids' => implode(',', $noSub),
                'class_rank'     => 0,
                'grade_rank'     => 0,
                'enabled'        => 1,
            ];
            #判断记录是否已经存在，存在则更新
            $scoreTotal = ScoreTotal::whereEnabled(1)
                ->whereStudentId($studentId)
                ->first();
            if ($scoreTotal) {
                $scoreTotal->update($scoreTotalData);
            } else {
                ScoreTotal::create($scoreTotalData);
            }
        }
        #创建完成后执行排名操作
        #班级排名
        foreach ($claStuIds as $claStuId) {
            $scoreTotalCla = ScoreTotal::whereEnabled(1)
                ->whereExamId($exam_id)
                ->whereIn('student_id', $claStuId)
                ->orderBy('score', 'desc')
                ->get();
            $toTmeScore = '';
            $toTmeRank = 0;
            foreach ($scoreTotalCla as $key => $scoreToal) {
                if ($toTmeScore != $scoreToal->score) {
                    $toTmeScore = $scoreToal->score;
                    $toTmeRank += 1;
                }
                $scoreToal->class_rank = $toTmeRank;
                if (!$scoreToal->save()) {
                    return false;
                }
            }
        }
        #年级排名
        foreach ($graStuIds as $graStuId) {
            $scoreTotalGra = ScoreTotal::whereEnabled(1)
                ->whereExamId($exam_id)
                ->whereIn('student_id', $graStuId)
                ->orderBy('score', 'desc')
                ->get();
            $toGraScore = '';
            $toGraRank = 0;
            foreach ($scoreTotalGra as $key => $scoreToal) {
                if ($toGraScore != $scoreToal->score) {
                    $toGraScore = $scoreToal->score;
                    $toGraRank += 1;
                }
                $scoreToal->grade_rank = $toGraRank;
                if (!$scoreToal->save()) {
                    return false;
                }
            }
        }
        return true;
    }
    
    /**
     * @param $exam
     * @param $squad
     * @param $subject
     * @param $project
     * @return array
     */
    public function scores($exam, $squad, $subject, $project) {
        $student = $this->where('exam_id', $exam)
            ->get()->pluck('student_id');
        # 当前班级下的所有参加考试的学生
        $students = Student::whereClassId($squad)->whereIn('id', $student)->get();
        # 当前选择班级的所属年级下 的所有班级 id
        $classes = Squad::where('grade_id', Squad::whereId($squad)->first()->grade_id)
            ->get()
            ->pluck('id');
        # 统计当前学生年级 的所有参加考试的学生
        $gradeStudents = Student::whereIn('class_id', $classes)
            ->whereIn('id', $student)
            ->get();
        $result = [];
        foreach ($students as $s) {
            $user = User::whereIn('id', array_column(json_decode($s->custodians), 'user_id'))
                ->get();
            $student = $s->user['realname'];
            $score = $this->where('exam_id', $exam)
                ->where('student_id', $s->id)
                ->get();
            $message = [];
            foreach ($subject as $j) {
                $sub = Subject::whereId($j)->first();
                $subScore = $this::where('exam_id', $exam)
                    ->where('subject_id', $j)
                    ->where('student_id', $s->id)
                    ->first();
                $subName = isset($sub->name) ? $sub->name : '';
                $sum = $score->sum('score');
                foreach ($project as $p) {
                    if ($p == 'score') {
                        if ($j == '-1') {
                            $message[] = '总分:' . $sum;
                        } else {
                            $message[] = $sub->name . ':' . $subScore->score;
                        }
                    }
                    if ($p == 'grade_rank') {
                        if ($j == '-1') {
                            $total = ScoreTotal::where('student_id', $s->id)
                                ->where('exam_id', $exam)
                                ->first();
                            $message[] = '年排名:' . $total->grade_rank;
                        } else {
                            $stotal = Score::where('student_id', $s->id)
                                ->where('exam_id', $exam)
                                ->where('subject_id', $j)
                                ->first();
                            $message[] = $subName . '(年排):' . $stotal->grade_rank;
                        }
                    }
                    if ($p == 'class_rank') {
                        if ($j == '-1') {
                            $total = ScoreTotal::where('student_id', $s->id)
                                ->where('exam_id', $exam)
                                ->first();
                            $message[] = '班排名:' . $total->class_rank;
                        } else {
                            $stotal = Score::where('student_id', $s->id)
                                ->where('exam_id', $exam)
                                ->where('subject_id', $j)
                                ->first();
                            $message[] = $subName . '(班排):' . $stotal->class_rank;
                        }
                    }
                    if ($p == 'grade_average') {
                        if ($j == '-1') {
                            $gaToTal = ScoreTotal::whereIn('student_id', $gradeStudents->pluck('id'))
                                ->where('exam_id', $exam)
                                ->get();
                            $ga = $gaToTal->sum('score') / $gaToTal->count();
                            $message[] = '年平均:' . $ga;
                        } else {
                            $sgaToTal = Score::whereIn('student_id', $gradeStudents->pluck('id'))
                                ->where('exam_id', $exam)
                                ->where('subject_id', $j)
                                ->get();
                            $sga = $sgaToTal->sum('score') / $sgaToTal->count();
                            $message[] = $subName . '(年平均):' . $sga;
                        }
                    }
                    if ($p == 'class_average') {
                        if ($j == '-1') {
                            $caToTal = ScoreTotal::whereIn('student_id', $students->pluck('id'))
                                ->where('exam_id', $exam)
                                ->get();
                            $ca = $caToTal->sum('score') / $caToTal->count();
                            $message[] = '班平均:' . $ca;
                        } else {
                            $scaToTal = Score::whereIn('student_id', $students->pluck('id'))
                                ->where('exam_id', $exam)
                                ->where('subject_id', $j)
                                ->get();
                            $sca = $scaToTal->sum('score') / $scaToTal->count();
                            $message[] = $subName . '(班平均):' . $sca;
                        }
                    }
                    if ($p == 'grade_max') {
                        if ($j == '-1') {
                            $maxTotal = ScoreTotal::whereIn('student_id', $gradeStudents->pluck('id'))
                                ->where('exam_id', $exam)
                                ->max('score');
                            $message[] = '年最高:' . $maxTotal;
                        } else {
                            $maxSub = Score::whereIn('student_id', $gradeStudents->pluck('id'))
                                ->where('exam_id', $exam)
                                ->where('subject_id', $j)
                                ->max('score');
                            $message[] = $subName . '(年最高):' . $maxSub;
                        }
                    }
                    if ($p == 'class_max') {
                        if ($j == '-1') {
                            $cmaxTotal = ScoreTotal::whereIn('student_id', $students->pluck('id'))
                                ->where('exam_id', $exam)
                                ->max('score');
                            $message[] = '班最高:' . $cmaxTotal;
                        } else {
                            $cmaxSub = Score::whereIn('student_id', $students->pluck('id'))
                                ->where('exam_id', $exam)
                                ->where('subject_id', $j)
                                ->max('score');
                            $message[] = $subName . '(班最高):' . $cmaxSub;
                        }
                        
                    }
                    if ($p == 'grade_min') {
                        if ($j == '-1') {
                            $minTotal = ScoreTotal::whereIn('student_id', $gradeStudents->pluck('id'))
                                ->where('exam_id', $exam)
                                ->min('score');
                            $message[] = '年最低:' . $minTotal;
                        } else {
                            $minSub = Score::whereIn('student_id', $gradeStudents->pluck('id'))
                                ->where('exam_id', $exam)
                                ->where('subject_id', $j)
                                ->min('score');
                            $message[] = $subName . '(年最低):' . $minSub;
                        }
                    }
                    if ($p == 'class_min') {
                        if ($j == '-1') {
                            $cminTotal = ScoreTotal::whereIn('student_id', $students->pluck('id'))
                                ->where('exam_id', $exam)
                                ->min('score');
                            $message[] = '班最低:' . $cminTotal;
                        } else {
                            $cminSub = Score::whereIn('student_id', $students->pluck('id'))
                                ->where('exam_id', $exam)
                                ->where('subject_id', $j)
                                ->min('score');
                            $message[] = $subName . '(班最低):' . $cminSub;
                        }
                    }
                    
                }
            }
            $result[] = [
                'custodian' => $user->pluck('realname'),
                'name'      => $student,
                'mobile'    => Mobile::whereIn('user_id', $user->pluck('id'))->get()->pluck('mobile'),
                'content'   => '尊敬的' . $student . '家长,'
                    . Exam::whereId($exam)->first()->name . '考试成绩已出:' . implode(',', $message) . '。',
            ];
            unset($message);
            
        }
        
        return $result;
        
    }
    
    /**
     * @param $data
     * @return array
     */
    public function sendMessage($data) {
        $corp = Corp::whereName('万浪软件')->first();
        $app = App::whereName('成绩中心')->first();
        $token = Wechat::getAccessToken($corp->corpid, $app->secret);
        $success = [];
        $failure = [];
        $school = School::whereId(School::schoolId())->first();
        foreach ($data as $d) {
            if (isset($d->mobile)) {
                $mobiles = explode(',', $d->mobile);
                foreach ($mobiles as $m) {
                    if ($m) {
                        $user = User::whereId(Mobile::where('mobile', $m)->first()->user_id)->first();
                        $userInfo = json_decode(Wechat::getUser($token, $user->userid));
                        if ($userInfo->errcode == 0) {
                            $message = [
                                'touser'  => $user->userid,
                                "msgtype" => "text",
                                "agentid" => $app->agentid,
                                'text'    => [
                                    'content' => $d->content,
                                ],
                            ];
                            $status = json_decode(Wechat::sendMessage($token, $message));
                            if ($status->errcode == 0) {
                                $success[] = $m;
                            } else {
                                $failure[] = $m;
                            }
                        } else {
                            $code = json_encode(Wechat::batchSend('LKJK004923', "654321@", $m, $d->content . $school->signature));
                            if ($code != '0' && $code != '-1') {
                                Log::debug($code);
                                $success[] = $m;
                            } else {
                                $failure[] = $m;
                            }
                        }
                        
                    }
                    
                }
                
            }
            
        }
        $result = [
            'message' => '成功:' . count($success) . '条数据;' . '失败:' . count($failure) . '条数据。',
            'success' => implode(',', $success),
            'failure' => implode(',', $failure),
        ];
        
        return $result;
        
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
     * @param $input
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
    
    /**
     * 成绩分析
     * @param $input
     * @return bool|string
     * @throws \Throwable
     */
    static public function analysis($input) {
        #分析班级成绩
        if ($input['type'] == 0) {
            $data = (new Score)->claAnalysis($input, false);
            if ($data) {
                return view('score.analysis_data', [
                    'className'   => $data['className'],
                    'examName'    => $data['examName'],
                    'oneData'     => $data['oneData'],
                    'rangs'       => $data['rangs'],
                    'totalRanges' => $data['totalRanges'],
                ])->render();
            } else {
                return false;
            }
        }
        
        #分析学生
        return false;
    }
    
    public function getExamClass($examId, $classId) {
        $student = $this->where('exam_id', $examId)
            ->get()->pluck('student_id');
        # 当前班级下的所有参加考试的学生
        $students = Student::whereClassId($classId)->whereIn('id', $student)->get();
        $result = [
            'exam'  => Exam::whereId($examId)->first()->name,
            'squad' => Squad::whereId($classId)->first()->name,
            'items' => [],
        ];
        foreach ($students as $s) {
            $total = ScoreTotal::whereExamId($examId)->where('student_id', $s->id)->first();
            $scores = $this::whereExamId($examId)->where('student_id', $s->id)->get();
            $detail = [];
            foreach ($scores as $c) {
                $detail[] = [
                    'subject' => $c->subject->name,
                    'score'   => $c->score,
                ];
            }
            $result['items'][] = [
                'student_id'     => $s->id,
                'exam_id'        => $examId,
                'realname'       => $s->user['realname'],
                'student_number' => $s->student_number,
//                'class_rank' => $total->class_rank,
//                'grade_rank' => $total->grade_rank,
                'class_rank'     => 3,
                'grade_rank'     => 5,
                'total'          => 623,
//                'total' => $total->score,
                'detail'         => $detail,
            ];
            unset($detail);
        }
        
        return $result;
    }
    
    public function getGraphData($studentId, $examId, $subjectId) {
        $exam = Exam::whereId($examId)->first();
        if ($subjectId == '-1') {
            $exams = Exam::whereId($examId)->where('start_date', '<=', $exam->start_date)
                ->orderBy('start_date', 'asc')
                ->limit(10)
                ->get();
            $es = [];
            $class_rank = [];
            $grade_rank = [];
            foreach ($exams as $e) {
                $total = ScoreTotal::whereExamId($e->id)->where('student_id', $studentId)->first();
                $es[] = $e->name;
                $class_rank[] = $total->class_rank;
                $grade_rank[] = $total->grade_rank;
            }
            
        } else {
            $es = [];
            $class_rank = [];
            $grade_rank = [];
            $scores = DB::table('scores')
                ->join('exams', 'exams.id', '=', 'scores.exam_id')
//                ->select('users.id', 'contacts.phone', 'orders.price')
                ->where('subject_id', $subjectId)
                ->where('student_id', $studentId)
                ->orderBy('exams.start_date', 'asc')
                ->limit(10)
                ->get();
            foreach ($scores as $s) {
                $es[] = $s->name;
                $class_rank[] = $s->class_rank;
                $grade_rank[] = $s->grade_rank;
            }
        }
        $result = [
            'exam'       => $es,
            'class_rank' => $class_rank,
            'grade_rank' => $grade_rank,
        ];
        
        return $result;
    }
    
    /**
     * 班级成绩分析
     *
     * @param $input
     * @param $wechat
     * @return array|bool
     */
    public function claAnalysis($input, $wechat = false) {
        #第一个表格数据
        $firstTableData = [];
        #存放满足当前科目的分数段设置和统计人数的数组（第二个表格数据--一个数据一个表格）
        $rangs = [];
        #存放总分分数段设置和统计人数的数组
        $scoreToRanges = [];
        $exam = Exam::whereId($input['exam_id'])->first();
        $squad = Squad::whereId($input['squad_id'])->first();
        #找到考试对应的科目存到数组 ids
        $examSub = explode(',', $exam->subject_ids);
        #找到班级下面对应所有的学生 ids
        $claStuIds = [];
        foreach ($squad->students as $student) {
            $claStuIds[] = $student->id;
        }
        #查出当前学校的所有分数段设置
        $grade = $squad->grade;
        $schoolId = $grade->school_id;
        $rangAll = ScoreRange::whereSchoolId($schoolId)->get();
        foreach ($examSub as $sub) {
            #一次处理一个科目  查出这个科目下 班级下所有学生的成绩
            $subject = Subject::whereId($sub)->first();
            # 若该学生id没有对应的score则不会在结果数组中
            $scores = Score::whereExamId($exam->id)
                ->whereSubjectId($sub)
                ->whereIn('student_id', $claStuIds)
                ->whereEnabled(1)
                ->get();
            if (count($scores) == 0) {
                return false;
            }
            #参与考试的总人数
            $countAll = $scores->count();
            #该科目的最高分
            $max = $scores->max('score');
            #该科目的最低分
            $min = $scores->min('score');
            #该科目的平均分
            $avg = $scores->average('score');
            #大于等于平均分的人数
            $bigNumber = Score::whereExamId($exam->id)
                ->whereSubjectId($sub)
                ->whereIn('student_id', $claStuIds)
                ->whereEnabled(1)
                ->where('score', '>=', $avg)
                ->count();
            #小于平均分的人数
            $minNumber = $countAll - $bigNumber;
            $firstTableData[] = [
                'sub'        => $subject->name,
                'count'      => $countAll,
                'max'        => $max,
                'min'        => $min,
                'avg'        => number_format($avg, 2),
                'big_number' => $bigNumber,
                'min_number' => $minNumber,
                'subId'      => $sub,
            ];
            #处理单科分数段
            foreach ($rangAll as $ran) {
                #筛选出针对于这一科的所有分数段设置
                if (in_array($sub, explode(',', $ran->subject_ids))) {
                    $minRange = $ran->start_score;
                    $maxRange = $ran->end_score;
                    #需要判断科目满分是否与最大相等
                    if ($subject->max_score == $maxRange) {
                        $count = Score::whereEnabled(1)
                            ->whereExamId($exam->id)
                            ->whereIn('student_id', $claStuIds)
                            ->whereSubjectId($sub)
                            ->where('score', '>=', $minRange)
                            ->where('score', '<=', $maxRange)
                            ->count();
                    } else {
                        $count = Score::whereEnabled(1)
                            ->whereExamId($exam->id)
                            ->whereIn('student_id', $claStuIds)
                            ->whereSubjectId($sub)
                            ->where('score', '>=', $minRange)
                            ->where('score', '<', $maxRange)
                            ->count();
                    }
                    $rangs[$sub][] = [
                        'range' =>
                            [
                                'min' => $minRange,
                                'max' => $maxRange,
                            ],
                        'score' =>
                            [
                                'sub'    => $subject->name,
                                'count'  => $countAll,
                                'number' => $count,
                            ],
                    ];
                }
            }
        }
        if (!$wechat) {
            #总分分数段
            $totalRanges = $rangAll->where('subject_ids', 0);
            #查询考试对应的总分 班级
            $scoreTotal = ScoreTotal::whereExamId($input['exam_id'])
                ->whereIn('student_id', $claStuIds)
                ->whereEnabled(1)
                ->get();
            #总分总人数
            $totalCount = $scoreTotal->count();
            #处理每个总分分数段的人数
            foreach ($totalRanges as $totalRange) {
                $minTotalRange = $totalRange->start_score;
                $maxTotalRange = $totalRange->end_score;
                $totalNumber = $scoreTotal->where('score', '>=', $minTotalRange)
                    ->where('score', '<', $maxTotalRange)
                    ->count();
                $scoreToRanges[] = [
                    'totalRange' => [
                        'min' => $minTotalRange,
                        'max' => $maxTotalRange,
                    ],
                    'totalScore' => [
                        'count'  => $totalCount,
                        'number' => $totalNumber,
                    ],
                ];
            }
        }
        $data = [
            'className'   => $exam->start_date,
            'examName'    => $exam->name,
            'oneData'     => $firstTableData,
            'rangs'       => $rangs,
            'totalRanges' => $scoreToRanges,
        ];
        
        return $data;
    }
    
    /**
     * 微信 监护人端 综合成绩分析
     * @param $input
     * @return array|bool
     */
    public function totalAnalysis($input) {
        #根据当前学生取的班级
        $student = Student::whereId($input['student_id'])->first();
        $squad = $student->squad;
        #找到班级下面对应所有的学生 ids
        $claStuIds = [];
        $data['total'] = [];
        $data['single'] = [];
        foreach ($squad->students as $student) {
            $claStuIds[] = $student->id;
        }
        #找到班级对应的年级
        $grade = $squad->grade;
        #找到年级下对应的所有的学生ids
        $graStuIds = [];
        foreach ($grade->classes as $class){
            foreach ($class->students as $student){
                $graStuIds[] = $student->id;
            }
        }
        #获得当前考试当前学生的总分
        $scoreTotal = ScoreTotal::whereExamId($input['exam_id'])
            ->whereStudentId($input['student_id'])
            ->first();
        if (!$scoreTotal) {
            return $data;
        }
        # 获取班级参与考试的所有记录
        $scoreTotalCla = ScoreTotal::whereEnabled(1)
            ->whereExamId($input['exam_id'])
            ->whereIn('student_id', $claStuIds)
            ->get();
        # 获取年级参与考试的所有记录
        $scoreTotalGra = ScoreTotal::whereEnabled(1)
            ->whereExamId($input['exam_id'])
            ->whereIn('student_id', $graStuIds)
            ->get();
        #总分平均分
        $avgCla = $scoreTotalCla->average('score');
        $avgGra = $scoreTotalGra->average('score');
        $data['total'] = [
            'total_score'       => $scoreTotal->score,
            'avgcla'      => number_format($avgCla, 1),
            'avggra'      => number_format($avgGra, 1),
            'class_rank'  => $scoreTotal->class_rank,
            'grade_rank'  => $scoreTotal->grade_rank,
            'class_count' => $scoreTotalCla->count(),
            'grade_count' => $scoreTotalGra->count(),
        ];
        #获取本次考试的各科成绩当前学生
        $scores = Score::whereEnabled(1)
            ->whereExamId($input['exam_id'])
            ->whereStudentId($input['student_id'])
            ->get();
        foreach ($scores as $score) {
            #获取当前科目下的平均分
            $scoreCla = Score::whereEnabled(1)
                ->whereExamId($input['exam_id'])
                ->whereIn('student_id', $claStuIds)
                ->whereSubjectId($score->subject->id)
                ->get();
            $data['single'][] = [
                'sub'   => $score->subject->name,
                'score' => $score->score,
                'avg' => number_format($scoreCla->average('score'),1)
            ];
            
        }
        return $data;
    }

    /**
     * 根据class_id获取考试的相关信息
     * @param $id
     * @return array
     */
    public function getClassScore($id){
        $score = [];
        $exams = Exam::where('class_ids','like','%' . $id . '%')
            ->get();
        foreach ($exams as $key=>$e)
        {
            $score[$key]['id'] = $e->id;
            $score[$key]['name'] = $e->name;
            $score[$key]['start_date'] = $e->start_date;
            $score[$key]['class_id'] = $id;
            $score[$key]['subject_ids'] = $e->subject_ids;

        }
        return $score;
    }

    /**
     * 根据监护人获取学生相关考试信息
     * @param $userId
     * @return array
     */
    public function getStudentScore($userId)
    {
        $students = User::whereUserid($userId)->first()->custodian->students;
        $score = $data = $studentName =[];
        foreach ($students as $k=>$s)
        {
            $exams = Exam::where('class_ids','like','%' . $s->class_id . '%')
                ->get();
            foreach ($exams as $key=>$e)
            {
                $score[$k][$key]['id'] = $e->id;
                $score[$k][$key]['student_id'] = $s->id;
                $score[$k][$key]['name'] = $e->name;
                $score[$k][$key]['start_date'] = $e->start_date;
                $score[$k][$key]['realname'] = $s->user->realname;
                $score[$k][$key]['class_id'] = $s->class_id;
                $score[$k][$key]['subject_ids'] = $e->subject_ids;
            }
            $studentName[]= [
                'title' => $s->user->realname,
                'value' => $s->id,
            ];
        }
        $data = [
            'score' => $score,
            'studentName' => $studentName
        ];

        return $data;
    }

    /**根据教职员工userId获取所在班级的考试
     * @param $userId
     * @return array
     */
    public function getEducatorScore($userId)
    {
        $score = $data = $className = [];
        $educatorId = User::whereUserid($userId)->first()->educator->id;
        $class = Squad::where('educator_ids','like','%' . $educatorId . '%')->get();
        foreach ($class as $k=>$c){
            $exams = Exam::where('class_ids','like','%' . $c->id . '%')
                ->get();
            foreach ($exams as $key=>$e)
            {
                $score[$k][$key]['id'] = $e->id;
                $score[$k][$key]['name'] = $e->name;
                $score[$k][$key]['classname'] = $c->name;
                $score[$k][$key]['start_date'] = $e->start_date;
                $score[$k][$key]['class_id'] = $c->id;
                $score[$k][$key]['subject_ids'] = $e->subject_ids;
            }

            $className[] = [
                'title' => $c->name,
                'value' => $c->id
            ];
        }
        $data = [
            'score' => $score,
            'className' => $className,
        ];

        return $data;
    }

    /**获取学生某次考试在班上的平均分
     * @param $examId
     * @param $subjectId
     * @param $studentsIds
     * @return mixed
     */
    public function getClassAvg($examId, $subjectId, $studentsIds)
    {
        $data = [];
        $scores = Score::whereExamId($examId)
            ->whereIn('student_id',$studentsIds)
            ->where('subject_id',$subjectId)
            ->where('enabled',1)
            ->get();
        $avg = $scores->average('score');
        $data = [
            'avg' => $avg ,
            'nums' => count($scores),
        ];
        return $data;
    }




}
