<?php
namespace App\Models;

use App\Events\ScoreImported;
use App\Events\ScoreUpdated;
use App\Facades\DatatableFacade as Datatable;
use App\Facades\Wechat;
use App\Helpers\Constant;
use App\Helpers\HttpStatusCode;
use App\Helpers\ModelTrait;
use Carbon\Carbon;
use Eloquent;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Throwable;

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
    
    use ModelTrait;
    
    # 导出格式
    const EXPORT_TITLES = [
        '姓名', '班级', '学号', '考试', '科目', '分数', '班排名', '年排名',
    ];
    
    # 导入格式
    const IMPORT_TITLES = [
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
    function student() { return $this->belongsTo('App\Models\Student'); }
    
    /**
     * 返回分数记录所属的科目对象
     *
     * @return BelongsTo
     */
    function subject() { return $this->belongsTo('App\Models\Subject'); }
    
    /**
     * 返回分数记录所述的考试对象
     *
     * @return BelongsTo
     */
    function exam() { return $this->belongsTo('App\Models\Exam'); }
    
    /**
     * 保存分数
     *
     * @param array $data
     * @return bool
     */
    function store(array $data) {
        
        $score = self::create($data);
        
        return $score ? true : false;
        
    }
    
    /**
     * 更新分数
     *
     * @param array $data
     * @param $id
     * @return bool
     * @throws Exception
     */
    function modify(array $data, $id = null) {
        
        if (isset($id)) {
            $score = self::find($id);
            if (!$score) {
                return false;
            }
            
            return $score->update($data) ? true : false;
        }
        
        return $this->batch($this);
        
    }
    
    /**
     * 删除分数
     *
     * @param $id
     * @return bool
     * @throws Exception
     */
    function remove($id = null) {
        
        if (isset($id)) {
            $score = self::find($id);
            if (!$score) {
                return false;
            }
            
            return $score->delete() ? true : false;
        }
        
        return $this->batch($this);
        
    }
    
    /**
     * 返回指定考试对应的学生及科目列表
     *
     * @param $examId
     * @return array
     */
    function ssList($examId) {
        
        $exam = Exam::find($examId);
        # 指定考试对应的班级
        $classIds = Squad::whereEnabled(1)
            ->whereIn('id', explode(',', $exam->class_ids))
            ->get()->pluck('id')->toArray();
        # 指定考试对应的科目列表
        $subjectList = Subject::whereEnabled(1)
            ->whereIn('id', explode(',', $exam->subject_ids))
            ->pluck('name', 'id')->toArray();
        # 指定考试对应的且对当前用户可见的学生列表
        $studentList = [];
        $students = Student::whereEnabled(1)
            ->whereIn('class_id', array_intersect($classIds, $this->classIds()))->get();
        foreach ($students as $student) {
            $studentList[$student->id] = $student->student_number . ' - ' . $student->user->realname;
        }
        
        return response()->json([
            'students' => $this->singleSelectList($studentList, 'student_id'),
            'subjects' => $this->singleSelectList($subjectList, 'subject_id'),
        ]);
        
    }
    
    /**
     * 分数记录列表
     *
     * @return array
     */
    function datatable() {
        
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
                    return Datatable::dtOps($d, $row, false, true, false);
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
        $condition = 'Student.id IN (' . implode(',', $this->contactIds('student')) . ')';
        
        return Datatable::simple(
            $this->getModel(), $columns, $joins, $condition
        );
        
    }
    
    /**
     * 排名统计
     *
     * @param $examId
     * @return boolean
     */
    function rank($examId) {
        
        $exam = Exam::whereId($examId)->first();
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
                $scores = Score::whereExamId($examId)
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
                $scoresAll = Score::whereExamId($examId)
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
        $studentScore = Score::whereExamId($examId)
            ->whereEnabled(1)
            ->whereIn('subject_id', $examSub)
            ->get()
            ->groupBy('student_id');
        /** @var Score $s */
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
                'exam_id'        => $examId,
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
                ->whereExamId($examId)
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
                ->whereExamId($examId)
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
                ->whereExamId($examId)
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
     * 预览
     *
     * @return JsonResponse
     */
    function preview() {
        
        $examId = Request::input('examId');
        $classId = Request::input('classId');
        $subjectIds = Request::input('subjectIds');
        $items = Request::input('items');
        if ($examId && $classId) {
            $result = $this->scores(
                $examId,
                $classId,
                $subjectIds ? explode(',', $subjectIds) : [],
                $items ? explode(',', $items) : []
            );
        } else {
            $ids = Exam::whereId($examId)->first();
            $classes = Squad::whereIn('id', explode(',', $ids['class_ids']))
                ->get()->toArray();
            $subjects = Subject::whereIn('id', explode(',', $ids['subject_ids']))
                ->get()->toArray();
            $result = [
                'classes'  => $classes,
                'subjects' => $subjects,
            ];
            
        }
        
        return response()->json($result);
        
    }
    
    /**
     * 发送成绩
     *
     * @param $data
     * @return array
     */
    function send($data) {
        
        $corp = Corp::whereName('万浪软件')->first();
        $app = App::whereName('成绩中心')->first();
        $token = Wechat::getAccessToken($corp->corpid, $app->secret);
        $success = [];
        $failure = [];
        $school = School::whereId($this->schoolId())->first();
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
     * 导入成绩
     *
     * @return bool
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     */
    function upload() {
        
        $file = Request::file('file');
        abort_if(
            !$file || !$file->isValid(),
            HttpStatusCode::BAD_REQUEST,
            __('messages.bad_request')
        );
        $ext = $file->getClientOriginalExtension();     // 扩展名//xls
        $realPath = $file->getRealPath();   //临时文件的绝对路径
        // 上传文件
        $filename = date('His') . uniqid() . '.' . $ext;
        $stored = Storage::disk('uploads')->put(
            $filename, file_get_contents($realPath)
        );
        if ($stored) {
            $spreadsheet = IOFactory::load(
                $this->uploadedFilePath($filename)
            );
            $scores = $spreadsheet->getActiveSheet()->toArray(
                null, true, true, true
            );
            abort_if(
                !empty(array_diff(self::IMPORT_TITLES, $scores[0])),
                HttpStatusCode::NOT_ACCEPTABLE,
                '文件格式错误'
            );
            # 这次考试对应的科目id
            $exam = Exam::find(Request::input('examId'));
            $subjectIds = explode(',', $exam->subject_ids);
            $aScores = $scores;
            #去除表头后的数据
            array_shift($aScores);
            $aScores = array_values($aScores);
            if (count($aScores) != 0) {
                # 去除表格的空数据
                foreach ($aScores as $key => $v) {
                    if ((array_filter($v)) == null) {
                        unset($aScores[$key]);
                    }
                }
            }
            #处理表头循环单列的数据插入分数
            for ($i = 3; $i < count($scores[0]); $i++) {
                $data = [];
                $subject = Subject::whereSchoolId($this->schoolId())
                    ->where('name', $scores[0][$i])->first();
                #判断录入科目分数是否在这次考试中  在
                if ($subject) {
                    if (in_array($subject->id, $subjectIds)) {
                        foreach ($aScores as $a) {
                            $data[] = [
                                'class'          => $a[0],
                                'student_number' => $a[1],
                                'student_name'   => $a[2],
                                'subject_id'     => $subject->id,
                                'score'          => $a[$i],
                                'exam_id'        => Request::input('examId'),
                            ];
                        }
                        abort_if(
                            !self::checkData($data, Request::all()),
                            HttpStatusCode::INTERNAL_SERVER_ERROR,
                            __('messages.internal_server_error')
                        );
                    }
                }
            }
            
            return true;
        }
        
        return false;
        
    }
    
    /**
     * 导出成绩
     *
     * @return mixed
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    function export() {
        
        $classId = Request::query('classId');
        $examId = Request::query('examId');
        $studentIds = Student::whereClassId($classId)->get()->pluck('id')->toArray();
        $scores = $this->whereExamId($examId)->whereIn('student_id', $studentIds)->get();
        $records = [self::EXPORT_TITLES];
        foreach ($scores as $score) {
            if (!$score->student) {
                continue;
            }
            $records[] = [
                $score->student->user->realname,
                $score->squad->name,
                $score->student->student_number,
                $score->exam->name,
                $score->subject->name,
                $score->score,
                $score->class_rank,
                $score->grade_rank,
            ];
        }
        
        return $this->excel($records);
        
    }
    
    /**
     * 检查每行数据 是否符合导入数据
     *
     * @param array $data
     * @param $input
     * @return bool
     */
    private function checkData(array $data, $input) {
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
     *
     * @return JsonResponse
     * @throws Throwable
     */
    function stat() {
        
        # 分析班级成绩
        if (Request::has('examId')) {
            $data = $this->classStat(false);
            abort_if(!$data, HttpStatusCode::NOT_FOUND, __('messages.not_found'));
            $view = view('score.class_stat', [
                'className'   => $data['className'],
                'examName'    => $data['examName'],
                'oneData'     => $data['oneData'],
                'rangs'       => $data['rangs'],
                'totalRanges' => $data['totalRanges'],
            ])->render();
        } else {
            $data = $this->studentStat();
            abort_if(!$data, HttpStatusCode::NOT_FOUND, __('messages.not_found'));
            $view = view('score.student_stat', [
                'examScore'   => $data['examScore'],
                'subjectName' => $data['subjectName'],
                'student'     => $data['student'],
            ])->render();
        }
        
        return response()->json(['html' => $view]);
        
    }
    
    /**
     * 返回指定考试/班级对应的班级/学生列表html
     *
     * @param $type
     * @param $value
     * @return JsonResponse
     */
    function lists($type, $value) {
        
        if ($type == 'class') {
            $html = (new Exam())->classList($value);
        } else {
            $html = (new Squad())->studentList($value);
        }
        
        return response()->json([
            'html' => $html,
        ]);
        
    }
    
    /**
     * 获取指定考试和班级的指定学生或所有学生的各科目考试详情
     *
     * @param integer $examId
     * @param integer $classId
     * @param null|string $realname - 学生姓名
     * @return array
     */
    function examDetail($examId, $classId, $realname = null) {
        
        $studentIds = $this->where('exam_id', $examId)
            ->get()->pluck('student_id');
        if ($realname) {
            $userIds = User::whereRealname($realname)->first()->pluck('id');
            # 当前班级下的所有参加考试的学生
            $students = Student::whereClassId($classId)
                ->whereIn('id', $studentIds)
                ->whereIn('user_id', $userIds)->get();
        } else {
            # 当前班级下的所有参加考试的学生
            $students = Student::whereClassId($classId)
                ->whereIn('id', $studentIds)->get();
        }
        $result = [
            'exam'  => Exam::find($examId)->name,
            'squad' => Squad::find($classId)->name,
            'items' => [],
        ];
        foreach ($students as $s) {
            $scores = $this->whereExamId($examId)
                ->where('student_id', $s->id)->get();
            $detail = [];
            foreach ($scores as $score) {
                $detail[] = [
                    'subject' => $score->subject->name,
                    'score'   => $score->score,
                ];
            }
            $result['items'][] = [
                'student_id'     => $s->id,
                'exam_id'        => $examId,
                'realname'       => $s->user['realname'],
                'student_number' => $s->student_number,
                'class_rank'     => 3,
                'grade_rank'     => 5,
                'total'          => 623,
                'detail'         => $detail,
            ];
        }
        
        return $result;
        
    }
    
    /**
     * 返回指定学生指定考试的分数详情
     *
     * @return Factory|JsonResponse|View
     */
    function studentDetail() {
        
        $total = [];
        $examId = Request::get('examId');
        $studentId = Request::get('targetId');
        # 获取该学生所属班级的所有学生
        $exam = Exam::find($examId);
        abort_if(!$exam, HttpStatusCode::NOT_FOUND, __('messages.not_found'));
        # 获取该次考试该学生所在的年级id
        $gradeId = Student::find($studentId)->squad->grade_id;
        $classIds = Grade::find($gradeId)->classes->pluck('id')->toArray();
        # 获取学生所属班级的所有学生ids
        $classStudentIds = Student::find($studentId)->squad->students->pluck('id')->toArray();
        # 获取该年级所有学生
        $gradeStudentIds = Student::whereIn('class_id', $classIds)->get()->pluck('id')->toArray();
        # 获取该次考试所有科目id
        $subjects = Subject::whereIn('id', explode(',', $exam->subject_ids))->pluck('name', 'id')->toArray();
        if (Request::method() == 'POST') {
            $subjectId = Request::get('subject_id');
        } else {
            reset($subjects);
            $subjectId = key($subjects);
        }
        /** @var Score $score */
        $score = $this->subjectScores($studentId, $subjectId, $examId);
        $score->{'start_date'} = $score->exam->start_date;
        $score->{'exam_name'} = $score->exam->name;
        $allScores = $this->subjectScores($subjectId, $studentId);
        foreach ($allScores as $score) {
            $total['name'][] = $score->exam->name;
            $total['score'][] = $score->score;
            list($classAvg) = $this->subjectAvg($score->exam_id, $subjectId, $classStudentIds);
            $total['avg'][] = $classAvg;
        }
        list($classAvg, $nClassScores) = $this->subjectAvg($examId, $subjectId, $classStudentIds);
        list($gradeAvg, $nGradeScores) = $this->subjectAvg($examId, $subjectId, $gradeStudentIds);
        $stat = [
            'classAvg'     => number_format($classAvg, 2),
            'nClassScores' => $nClassScores,
            'gradeAvg'     => number_format($gradeAvg, 2),
            'nGradeScores' => $nGradeScores,
        ];
        
        return Request::method() == 'POST'
            ? response()->json([
                'score' => $score,
                'stat'  => $stat,
                'total' => $total,
            ])
            : view('wechat.score.student_subject_detail', [
                'score'     => $score,
                'stat'      => $stat,
                'subjects'  => $subjects,
                'total'     => $total,
                'examId'    => $examId,
                'studentId' => $studentId,
            ]);
        
    }
    
    function classDetail() {
    
        $classId = Request::input('classId');
        $examId = Request::input('examId');
        $student = Request::input('student');
        if ($classId && $examId) {
            $data = $this->examDetail($examId, $classId, $student);
        
            return view('wechat.score.detail', [
                'data'    => $data,
                'classId' => $classId,
                'examId'  => $examId,
            ]);
        }
    
        return abort(HttpStatusCode::BAD_REQUEST, '请求无效');
        
    }
    
    function getGraphData($studentId, $examId, $subjectId) {
        $exam = Exam::whereId($examId)->first();
        if ($subjectId == '-1') {
//            $exams = Exam::where('start_date', '<=', $exam->start_date)
//                ->orderBy('start_date', 'asc')
//                ->limit(10)
//                ->get();
            $scores = DB::table('score_totals')
                ->join('exams', 'exams.id', '=', 'score_totals.exam_id')
//                ->select('users.id', 'contacts.phone', 'orders.price')
                ->where('student_id', $studentId)
                ->where('exams.start_date', '<=', $exam->start_date)
                ->orderBy('exams.start_date', 'asc')
                ->limit(10)
                ->get();
            $es = [];
            $class_rank = [];
            $grade_rank = [];
//            foreach ($exams as $e) {
//                $total = ScoreTotal::whereExamId($e->id)->where('student_id', $studentId)->first();
//                $es[] = $e->name;
//                $class_rank[] = $total->class_rank;
//                $grade_rank[] = $total->grade_rank;
//            }
            foreach ($scores as $s) {
                $es[] = $s->name;
                $class_rank[] = $s->class_rank;
                $grade_rank[] = $s->grade_rank;
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
                ->where('exams.start_date', '<=', $exam->start_date)
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
     * @param $wechat
     * @return array|bool
     */
    function classStat($wechat = false) {
        
        #第一个表格数据
        $firstTableData = [];
        #存放满足当前科目的分数段设置和统计人数的数组（第二个表格数据--一个数据一个表格）
        $rangs = [];
        #存放总分分数段设置和统计人数的数组
        $scoreToRanges = [];
        $exam = Exam::find(Request::input('examId'));
        if (!$exam) {
            return false;
        }
        $squad = Squad::find(Request::input('classId'));
        if (!$squad) {
            return false;
        }
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
        $rangAll = ScoreRange::whereSchoolId($schoolId)
            ->whereEnabled(1)
            ->get();
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
                $countAll = 0;
                $max = 0;
                $min = 0;
                $avg = 0;
                $bigNumber = 0;
                $minNumber = 0;
            } else {
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
            }
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
            if (count($rangAll) != 0) {
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
        }
        if (!$wechat) {
            #总分分数段
            $totalRanges = $rangAll->where('subject_ids', 0);
            #查询考试对应的总分 班级
            $scoreTotal = ScoreTotal::whereExamId(Request::input('examId'))
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
        
        return [
            'className'   => $squad->name,
            'examName'    => $exam->name,
            'oneData'     => $firstTableData,
            'rangs'       => $rangs,
            'totalRanges' => $scoreToRanges,
        ];
        
    }
    
    /**
     * 学生成绩分析
     *
     * @return mixed
     */
    function studentStat() {
        
        #学生对象
        $student = Student::find(Request::input('studentId'));
        #先找出这个学生最近十场考试
        $exams = Exam::all();
        $studentExams = [];
        foreach ($exams as $exam) {
            if (in_array(Request::input('classId'), explode(',', $exam->class_ids))) {
                if (count($studentExams) < 10) {
                    $studentExams[] = $exam->id;
                }
            }
        }
        $stuScores = [];
        #十次考试科目汇总
        $examSubjects = [];
        $subjects = [];
        #存这十次考试的科目和id
        $subName = [];
        foreach ($studentExams as $stuExam) {
            $exam = Exam::whereId($stuExam)->first();
            $examSubjects[] = explode(',', $exam->subject_ids);
        }
        foreach ($examSubjects as $sub) {
            foreach ($sub as $s) {
                $subjects[] = $s;
            }
        }
        foreach (array_unique($subjects) as $item) {
            $subName[$item] = Subject::whereId($item)->first()->name;
        }
        #处理$subName 按照key值升序
        ksort($subName);
        foreach ($studentExams as $stuExam) {
            $exam = Exam::whereId($stuExam)->first();
            $studentScore = [];
            $examName = $exam->name;
            $examTime = $exam->start_date;
            foreach ($subName as $key => $value) {
                $sco = $this->whereExamId($stuExam)
                    ->whereStudentId(Request::input('studentId'))
                    ->whereEnabled(1)
                    ->where('subject_id', $key)
                    ->first();
                if (empty($sco)) {
                    $studentScore[$key] = [
                        'score'      => '——',
                        'class_rank' => '——',
                        'grade_rank' => '——',
                    ];
                } else {
                    $studentScore[$key] = [
                        'score'      => $sco->score,
                        'class_rank' => $sco->class_rank,
                        'grade_rank' => $sco->grade_rank,
                    ];
                }
            }
            #处理$studentScore 按照key值升序
            ksort($studentScore);
            $examTotal = ScoreTotal::whereStudentId(Request::input('studentId'))
                ->whereExamId($stuExam)
                ->first();
            if ($examTotal) {
                $scoreTotal = [
                    'score'      => $examTotal->score,
                    'class_rank' => $examTotal->class_rank,
                    'grade_rank' => $examTotal->grade_rank,
                ];
            } else {
                $scoreTotal = [
                    'score'      => '——',
                    'class_rank' => '——',
                    'grade_rank' => '——',
                ];
            }
            $stuScores[] = [
                'examId'     => $stuExam,
                'examName'   => $examName,
                'examTime'   => $examTime,
                'score'      => $studentScore,
                'scoreTotal' => $scoreTotal,
            ];
        }
        
        return [
            'examScore'   => $stuScores,
            'subjectName' => $subName,
            'student'     => $student,
        ];
        
    }
    
    /**
     * 微信 监护人端 综合成绩分析
     * @param $input
     * @return array|bool
     */
    function totalAnalysis($input) {
        
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
        foreach ($grade->classes as $class) {
            foreach ($class->students as $student) {
                $graStuIds[] = $student->id;
            }
        }
        #获得当前考试当前学生的总分
        $scoreTotal = ScoreTotal::whereExamId($input['exam_id'])
            ->whereStudentId($input['student_id'])
            ->first();
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
        if (!$scoreTotal) {
            $data['total'] = [
                'total_score' => '--',
                'avgcla'      => number_format($avgCla, 1),
                'avggra'      => number_format($avgGra, 1),
                'class_rank'  => '--',
                'grade_rank'  => '--',
                'class_count' => '',
                'grade_count' => '',
            ];
        } else {
            $data['total'] = [
                'total_score' => $scoreTotal->score,
                'avgcla'      => number_format($avgCla, 1),
                'avggra'      => number_format($avgGra, 1),
                'class_rank'  => $scoreTotal->class_rank,
                'grade_rank'  => $scoreTotal->grade_rank,
                'class_count' => $scoreTotalCla->count(),
                'grade_count' => $scoreTotalGra->count(),
            ];
        }
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
                'avg'   => number_format($scoreCla->average('score'), 1),
            ];
            
        }
        
        return $data;
    }
    
    /**
     * 获取学生某次考试在班上的平均分
     *
     * @param $examId
     * @param $subjectId
     * @param $studentsIds
     * @return mixed
     */
    function subjectAvg($examId, $subjectId, array $studentsIds) {
        
        $scores = Score::whereExamId($examId)
            ->whereIn('student_id', $studentsIds)
            ->where('subject_id', $subjectId)
            ->where('enabled', 1)
            ->get();
        
        return [
            $scores->avg('score') ?? 0,
            count($scores),
        ];
        
    }
    
    /**
     * 查询某学生某科目全部的考试分数
     *
     * @param $studentId
     * @param $subjectId
     * @param null $examId
     * @return array|Collection|static[]
     */
    function subjectScores($studentId, $subjectId, $examId = null) {
        
        return !$examId
            ? Score::whereStudentId($studentId)
                ->where('subject_id', $subjectId)
                ->where('enabled', Constant::ENABLED)
                ->get()
            : Score::whereStudentId($studentId)
                ->where('exam_id', $examId)
                ->where('subject_id', $subjectId)
                ->where('enabled', Constant::ENABLED)
                ->first();
        
    }
    
    /**
     * 返回微信端成绩中心首页
     *
     * @return Factory|JsonResponse|View
     * @throws Throwable
     */
    function wIndex() {
        
        $user = Auth::user();
        $pageSize = 4;
        $start = Request::get('start') ? Request::get('start') * $pageSize : 0;
        $exam = new Exam();
        abort_if(
            !$user->custodian && !$user->educator,
            HttpStatusCode::UNAUTHORIZED,
            __('messages.unauthorized')
        );
        if (Request::method() == 'POST') {
            $targetId = Request::input('target_id');
            $classId = $user->custodian ? Student::find($targetId)->class_id : $targetId;
            $keyword = Request::has('keyword') ? Request::input('keyword') : null;
            $exams = array_slice($exam->examsByClassId($classId, $keyword), $start, $pageSize);
            
            return response()->json([
                'exams' => $exams,
            ]);
        }
        $targets = $exams = [];
        if ($user->custodian) {
            $targets = $user->custodian->myStudents();
            reset($targets);
            $exams = array_slice((new Student())->exams(key($targets)), $start, $pageSize);
        } elseif ($user->educator) {
            $targets = Squad::whereIn('id', $this->classIds($user->educator->school_id))
                ->where('enabled', 1)->pluck('name', 'id')->toArray();
            reset($targets);
            $exams = array_slice($exam->examsByClassId(key($targets)), $start, $pageSize);
        }
        
        return view('wechat.score.index', [
            'targets' => $targets,
            'exams'   => $exams,
        ]);
        
    }
    
    /**
     * @param $examId
     * @param $classId
     * @param $subjectIds
     * @param $items
     * @return array
     */
    private function scores($examId, $classId, $subjectIds, $items) {
        
        $studentIds = $this->whereExamId($examId)
            ->get()->pluck('student_id');
        # 当前班级下的所有参加考试的学生
        $students = Student::whereClassId($classId)->whereIn('id', $studentIds)->get();
        # 当前选择班级的所属年级下 的所有班级 id
        $classes = Squad::where('grade_id', Squad::whereId($classId)->first()->grade_id)
            ->get()->pluck('id');
        # 统计当前学生年级 的所有参加考试的学生
        $gradeStudents = Student::whereIn('class_id', $classes)
            ->whereIn('id', $studentIds)->get();
        $result = [];
        foreach ($students as $s) {
            $user = User::whereIn('id', array_column(json_decode($s->custodians), 'user_id'))->get();
            $realname = $s->user['realname'];
            $message = [];
            foreach ($subjectIds as $subjectId) {
                $s = Subject::find($subjectId);
                $subScore = self::where('exam_id', $examId)
                    ->where('subject_id', $subjectId)
                    ->where('student_id', $s->id)
                    ->first();
                $sName = $s->name ?? '';
                foreach ($items as $item) {
                    switch ($item) {
                        case 'score':
                            if ($subjectId == '-1') {
                                $message[] = '总分:' . ScoreTotal::
                                    whereExamId($examId)
                                        ->where('student_id', $s->id)
                                        ->first()
                                        ->score;
                            } else {
                                $message[] = $s->name . ':' . $subScore->score;
                            }
                            break;
                        case 'grade_rank':
                            if ($subjectId == '-1') {
                                $total = ScoreTotal::where('student_id', $s->id)
                                    ->where('exam_id', $examId)
                                    ->first();
                                $message[] = '年排名:' . $total->grade_rank;
                            } else {
                                $stotal = self::where('student_id', $s->id)
                                    ->where('exam_id', $examId)
                                    ->where('subject_id', $subjectId)
                                    ->first();
                                $message[] = $sName . '(年排):' . $stotal->grade_rank;
                            }
                            break;
                        case 'class_rank':
                            if ($subjectId == '-1') {
                                $total = ScoreTotal::where('student_id', $s->id)
                                    ->where('exam_id', $examId)
                                    ->first();
                                $message[] = '班排名:' . $total->class_rank;
                            } else {
                                $stotal = self::where('student_id', $s->id)
                                    ->where('exam_id', $examId)
                                    ->where('subject_id', $subjectId)
                                    ->first();
                                $message[] = $sName . '(班排):' . $stotal->class_rank;
                            }
                            break;
                        case 'grade_average':
                            if ($subjectId == '-1') {
                                $gaToTal = ScoreTotal::whereIn('student_id', $gradeStudents->pluck('id'))
                                    ->where('exam_id', $examId)
                                    ->get();
                                if ($gaToTal->count() == 0) {
                                    $ga = 0;
                                } else {
                                    $ga = $gaToTal->sum('score') / $gaToTal->count();
                                }
                                $message[] = '年平均:' . sprintf("%.2f", $ga);
                            } else {
                                $sgaToTal = self::whereIn('student_id', $gradeStudents->pluck('id'))
                                    ->where('exam_id', $examId)
                                    ->where('subject_id', $subjectId)
                                    ->get();
                                if ($sgaToTal->count() == 0) {
                                    $sga = 0;
                                } else {
                                    $sga = $sgaToTal->sum('score') / $sgaToTal->count();
                                }
                                $message[] = $sName . '(年平均):' . sprintf("%.2f", $sga);
                            }
                            break;
                        case 'class_average':
                            if ($subjectId == '-1') {
                                $caToTal = ScoreTotal::whereIn('student_id', $students->pluck('id'))
                                    ->where('exam_id', $examId)
                                    ->get();
                                if ($caToTal->count() == 0) {
                                    $ca = 0;
                                } else {
                                    $ca = $caToTal->sum('score') / $caToTal->count();
                                }
                                $message[] = '班平均:' . sprintf("%.2f", $ca);
                            } else {
                                $scaToTal = self::whereIn('student_id', $students->pluck('id'))
                                    ->where('exam_id', $examId)
                                    ->where('subject_id', $subjectId)
                                    ->get();
                                if ($scaToTal->count() == 0) {
                                    $sca = 0;
                                } else {
                                    $sca = $scaToTal->sum('score') / $scaToTal->count();
                                }
                                $message[] = $sName . '(班平均):' . sprintf("%.2f", $sca);
                            }
                            break;
                        case 'grade_max':
                            if ($subjectId == '-1') {
                                $maxTotal = ScoreTotal::whereIn('student_id', $gradeStudents->pluck('id'))
                                    ->where('exam_id', $examId)
                                    ->max('score');
                                $message[] = '年最高:' . $maxTotal;
                            } else {
                                $maxSub = self::whereIn('student_id', $gradeStudents->pluck('id'))
                                    ->where('exam_id', $examId)
                                    ->where('subject_id', $subjectId)
                                    ->max('score');
                                $message[] = $sName . '(年最高):' . $maxSub;
                            }
                            break;
                        case 'class_max':
                            if ($subjectId == '-1') {
                                $cmaxTotal = ScoreTotal::whereIn('student_id', $students->pluck('id'))
                                    ->where('exam_id', $examId)
                                    ->max('score');
                                $message[] = '班最高:' . $cmaxTotal;
                            } else {
                                $cmaxSub = self::whereIn('student_id', $students->pluck('id'))
                                    ->where('exam_id', $examId)
                                    ->where('subject_id', $subjectId)
                                    ->max('score');
                                $message[] = $sName . '(班最高):' . $cmaxSub;
                            }
                            break;
                        case 'grade_min':
                            if ($subjectId == '-1') {
                                $minTotal = ScoreTotal::whereIn('student_id', $gradeStudents->pluck('id'))
                                    ->where('exam_id', $examId)
                                    ->min('score');
                                $message[] = '年最低:' . $minTotal;
                            } else {
                                $minSub = self::whereIn('student_id', $gradeStudents->pluck('id'))
                                    ->where('exam_id', $examId)
                                    ->where('subject_id', $subjectId)
                                    ->min('score');
                                $message[] = $sName . '(年最低):' . $minSub;
                            }
                            break;
                        case 'class_min':
                            if ($subjectId == '-1') {
                                $cminTotal = ScoreTotal::whereIn('student_id', $students->pluck('id'))
                                    ->where('exam_id', $examId)
                                    ->min('score');
                                $message[] = '班最低:' . $cminTotal;
                            } else {
                                $cminSub = self::whereIn('student_id', $students->pluck('id'))
                                    ->where('exam_id', $examId)
                                    ->where('subject_id', $subjectId)
                                    ->min('score');
                                $message[] = $sName . '(班最低):' . $cminSub;
                            }
                            break;
                        default:
                            break;
                        
                    }
                }
            }
            $msgTpl = '尊敬的%s家长, %s考试成绩已出: %s。';
            $content = sprintf(
                $msgTpl, $realname,
                Exam::find($examId)->name,
                implode(',', $message)
            );
            $result[] = [
                'custodian' => $user->pluck('realname'),
                'name'      => $realname,
                'mobile'    => Mobile::whereIn('user_id', $user->pluck('id'))->get()->pluck('mobile'),
                'content'   => $content,
            ];
            unset($message);
            
        }
        
        return $result;
        
    }
    
}
