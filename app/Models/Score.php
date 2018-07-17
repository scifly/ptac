<?php
namespace App\Models;

use App\Facades\Datatable;
use App\Facades\Wechat;
use App\Helpers\HttpStatusCode;
use App\Helpers\ModelTrait;
use App\Helpers\Snippet;
use App\Jobs\ImportScore;
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
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Storage;
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
     * 分数记录列表
     *
     * @return array
     */
    function index() {
        
        $columns = [
            ['db' => 'Score.id', 'dt' => 0],
            ['db' => 'User.realname', 'dt' => 1],
            [
                'db'        => 'Grade.name as gradename', 'dt' => 2,
                'formatter' => function ($d) {
                    return Snippet::grade($d);
                },
            ],
            [
                'db'        => 'Squad.name', 'dt' => 3,
                'formatter' => function ($d) {
                    return Snippet::squad($d);
                },
            ],
            ['db' => 'Student.student_number', 'dt' => 4],
            ['db' => 'Subject.name as subjectname', 'dt' => 5],
            ['db' => 'Exam.name as examname', 'dt' => 6],
            [
                'db'        => 'Score.class_rank', 'dt' => 7,
                'formatter' => function ($d) {
                    return $d === 0 ? "未统计" : $d;
                },
            ],
            [
                'db'        => 'Score.grade_rank', 'dt' => 8,
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
     * 保存分数
     *
     * @param array $data
     * @return bool
     */
    function store(array $data) {
        
        return $this->create($data) ? true : false;
        
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
        
        return $id
            ? $this->find($id)->update($data)
            : $this->batch($this);
        
    }
    
    /**
     * 删除分数
     *
     * @param $id
     * @return bool
     * @throws Exception
     */
    function remove($id = null) {
        
        return $this->del($this, $id);
        
    }
    
    /**
     * 删除指定分数记录的所有数据
     *
     * @param $id
     * @throws Exception
     */
    function purge($id) {
        
        try {
            DB::transaction(function () use ($id) {
                $score = $this->find($id);
                (new ScoreTotal)->removeSubject(
                    $score->subject_id,
                    $score->exam_id,
                    $score->score
                );
                $score->delete();
            });
        } catch (Exception $e) {
            throw $e;
        }
        
    }
    
    /**
     * 删除指定科目对应的所有考试成绩
     * 更新对应的总分记录
     *
     * @param $subjectId
     * @throws Exception
     */
    function removeSubject($subjectId) {
        
        try {
            DB::transaction(function () use ($subjectId) {
                $scoreIds = $this->where('subject_id', $subjectId)->pluck('id')->toArray();
                array_map([$this, 'purge'], $scoreIds);
                $this->where('subject_id', $subjectId)->delete();
            });
        } catch (Exception $e) {
            throw $e;
        }
        
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
            ->pluck('id')->toArray();
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
     * 排名统计
     *
     * @param $examId
     * @return boolean
     */
    function rank($examId) {
        
        $exam = Exam::find($examId);
        #找到考试对应的科目存到数组 ids
        $subjectIds = explode(',', $exam->subject_ids);
        #找到考试对应的班级存到数组 ids
        $classIds = explode(',', $exam->class_ids);
        #找到每个班级下面对应的学生 ids
        $classStudentIds = [];
        foreach ($classIds as $classId) {
            $class = Squad::find($classId);
            foreach ($class->students as $student) {
                $classStudentIds[$class->id][] = $student->id;
            }
        }
        #根据班级找出参与考试的所有年级
        $gradeIds = [];
        foreach ($classIds as $classId) {
            $gradeIds[] = Squad::find($classId)->grade_id;
        }
        #找到每个年级下面的所有学生
        $gradeStudentIds = [];
        foreach (array_unique($gradeIds) as $gradeId) {
            $squads = Grade::find($gradeId)->classes;
            foreach ($squads as $class) {
                foreach ($class->students as $student) {
                    $gradeStudentIds[$gradeId][] = $student->id;
                }
            }
        }
        foreach ($subjectIds as $subjectId) {
            #一次处理一个科目  查出这个科目下 班级下所有学生的成绩
            foreach ($classStudentIds as $studentIds) {
                # 若该学生id没有对应的score则不会在结果数组中
                $scores = Score::whereExamId($examId)
                    ->whereIn('student_id', $studentIds)
                    ->where('subject_id', $subjectId)
                    ->where('enabled', 1)
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
            foreach ($gradeStudentIds as $gradeStudentId) {
                $scoresAll = Score::whereExamId($examId)
                    ->whereSubjectId($subjectId)
                    ->whereIn('student_id', $gradeStudentId)
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
            ->where('enabled', 1)
            ->whereIn('subject_id', $subjectIds)
            ->get()->groupBy('student_id');
        /** @var Score $s */
        foreach ($studentScore as $s) {
            #当前学生参加的考试科目数组
            $studentSub = [];
            $studentId = $s[0]->student_id;
            $total = $s->sum('score');
            foreach ($s as $subjectId) {
                $studentSub[] = $subjectId->subject_id;
            }
            #没有参加的考试科目
            $noSub = array_diff($subjectIds, $studentSub);
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
        foreach ($classStudentIds as $studentIds) {
            $scoreTotalCla = ScoreTotal::whereEnabled(1)
                ->whereExamId($examId)
                ->whereIn('student_id', $studentIds)
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
        foreach ($gradeStudentIds as $gradeStudentId) {
            $scoreTotalGra = ScoreTotal::whereEnabled(1)
                ->whereExamId($examId)
                ->whereIn('student_id', $gradeStudentId)
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
                $examId, $classId,
                $subjectIds ?? [], $items ?? []
            );
        } else {
            $exam = Exam::find($examId);
            $classes = Squad::whereIn('id', explode(',', $exam->class_ids))->get();
            $subjects = Subject::whereIn('id', explode(',', $exam->subject_ids))->get();
            $result = [
                'classes'  => $classes->toArray(),
                'subjects' => $subjects->toArray(),
            ];
        }
        
        return response()->json($result);
        
    }
    
    /**
     * @param $examId
     * @param $classId
     * @param $subjectIds
     * @param $items
     * @return array
     */
    private function scores($examId, $classId, $subjectIds, $items) {
        
        $studentIds = Score::whereExamId($examId)->pluck('student_id')->toArray();
        # 当前班级下的所有参加考试的学生
        $students = Student::whereClassId($classId)->whereIn('id', $studentIds)->get();
        # 当前选择班级的所属年级下 的所有班级 id
        $classeIds = Squad::whereGradeId(Squad::find($classId)->grade_id)->pluck('id')->toArray();
        # 统计当前学生年级 的所有参加考试的学生
        $gradeStudentIds = Student::whereIn('class_id', $classeIds)->whereIn('id', $studentIds)->get();
        $result = [];
        foreach ($students as $student) {
            $custodians = User::whereIn('id', array_column(json_decode($student->custodians), 'user_id'))->get();
            $studentName = $student->user->realname;
            $message = [];
            foreach ($subjectIds as $subjectId) {
                if ($subjectId != -1) {
                    $subject = Subject::find($subjectId);
                    $score = Score::whereExamId($examId)
                        ->where('subject_id', $subjectId)
                        ->where('student_id', $student->id)
                        ->first();
                    foreach ($items as $item) {
                        switch ($item) {
                            case 'score':
                                $message[] = $subject->name . ':' . $score->score;
                                break;
                            case 'grade_rank':
                                $message[] = $subject->name . '(年排):' . $score->grade_rank;
                                break;
                            case 'class_rank':
                                $message[] = $subject->name . '(班排):' . $score->class_rank;
                                break;
                            case 'grade_average':
                                $gradeScores = Score::whereExamId($examId)->where('subject_id', $subjectId)
                                    ->whereIn('student_id', $gradeStudentIds)->get();
                                $average = $gradeScores->count()
                                    ? $gradeScores->sum('score') / $gradeScores->count() : 0;
                                $message[] = $subject->name . '(年平均):' . sprintf("%.2f", $average);
                                break;
                            case 'class_average':
                                $classScores = Score::whereExamId($examId)->where('subject_id', $subjectId)
                                    ->whereIn('student_id', $students->pluck('id'))->get();
                                $average = $classScores->count()
                                    ? $classScores->sum('score') / $classScores->count() : 0;
                                $message[] = $subject->name . '(班平均):' . sprintf("%.2f", $average);
                                break;
                            case 'grade_max':
                                $subjectGradeMax = Score::whereExamId($examId)->where('subject_id', $subjectId)
                                    ->whereIn('student_id', $gradeStudentIds)->max('score');
                                $message[] = $subject->name . '(年最高):' . $subjectGradeMax;
                                break;
                            case 'class_max':
                                $subjectClassMax = Score::whereExamId($examId)->where('subject_id', $subjectId)
                                    ->whereIn('student_id', $students->pluck('id'))->max('score');
                                $message[] = $subject->name . '(班最高):' . $subjectClassMax;
                                break;
                            case 'grade_min':
                                $subjectGradeMin = Score::whereExamId($examId)->where('subject_id', $subjectId)
                                    ->whereIn('student_id', $gradeStudentIds->pluck('id'))->min('score');
                                $message[] = $subject->name . '(年最低):' . $subjectGradeMin;
                                break;
                            case 'class_min':
                                $subjectClassMin = Score::whereExamId($examId)->where('subject_id', $subjectId)
                                    ->whereIn('student_id', $students->pluck('id'))->min('score');
                                $message[] = $subject->name . '(班最低):' . $subjectClassMin;
                                break;
                            default:
                                break;
                        }
                    }
                } else {
                    $total = ScoreTotal:: whereExamId($examId)->where('student_id', $student->id)->first();
                    abort_if(
                        !$total,
                        HttpStatusCode::INTERNAL_SERVER_ERROR,
                        __('messages.score.total_score_unavailable')
                    );
                    foreach ($items as $item) {
                        switch ($item) {
                            case 'score':
                                $message[] = '总分:' . $total->score;
                                break;
                            case 'grade_rank':
                                $message[] = '年排名:' . $total->grade_rank;
                                break;
                            case 'class_rank':
                                $message[] = '班排名:' . $total->class_rank;
                                break;
                            case 'grade_average':
                                $gradeTotals = ScoreTotal::whereIn('student_id', $gradeStudentIds)
                                    ->where('exam_id', $examId)->get();
                                $average = $gradeTotals->count()
                                    ? $gradeTotals->sum('score') / $gradeTotals->count() : 0;
                                $message[] = '年平均:' . sprintf("%.2f", $average);
                                break;
                            case 'class_average':
                                $classTotals = ScoreTotal::whereIn('student_id', $students->pluck('id'))
                                    ->where('exam_id', $examId)->get();
                                $average = $classTotals->count()
                                    ? $classTotals->sum('score') / $classTotals->count() : 0;
                                $message[] = '班平均:' . sprintf("%.2f", $average);
                                break;
                            case 'grade_max':
                                $totalGradeMax = ScoreTotal::whereExamId($examId)
                                    ->whereIn('student_id', $gradeStudentIds)->max('score');
                                $message[] = '年最高:' . $totalGradeMax;
                                break;
                            case 'class_max':
                                $totalClassMax = ScoreTotal::whereExamId($examId)
                                    ->whereIn('student_id', $students->pluck('id'))->max('score');
                                $message[] = '班最高:' . $totalClassMax;
                                break;
                            case 'grade_min':
                                $totalGradeMin = ScoreTotal::whereExamId($examId)
                                    ->whereIn('student_id', $gradeStudentIds->pluck('id'))->min('score');
                                $message[] = '年最低:' . $totalGradeMin;
                                break;
                            case 'class_min':
                                $totalClassMin = ScoreTotal::whereExamId($examId)
                                    ->whereIn('student_id', $students->pluck('id'))->min('score');
                                $message[] = '班最低:' . $totalClassMin;
                                break;
                            default:
                                break;
                        }
                    }
                }
            }
            $content = sprintf(
                __('messages.score.message_template'),
                $studentName,
                Exam::find($examId)->name,
                implode(',', $message)
            );
            foreach ($custodians as $custodian) {
                $result[] = [
                    'custodian' => $custodian->realname,
                    'name'      => $studentName,
                    'mobile'    => Mobile::whereUserId($custodian->id)->where('isdefault', 1)->first()->mobile,
                    'content'   => $content,
                ];
            }
        }
        
        return $result;
        
    }
    
    /**
     * 发送成绩
     *
     * @param $data
     * @return array
     * @throws Exception
     */
    function send($data) {
        
        $corp = Corp::find(
            School::find($this->schoolId())->corp_id
        );
        $app = App::whereName('成绩中心')->where('corp_id', $corp->id)->first();
        $token = Wechat::getAccessToken($corp->corpid, $app->secret);
        abort_if(
            $token['errcode'],
            HttpStatusCode::INTERNAL_SERVER_ERROR,
            __('messages.internal_server_error')
        );
        $success = [];
        $failure = [];
        $school = School::find($this->schoolId());
        foreach ($data as $datum) {
            if (isset($datum->mobile)) {
                $mobiles = explode(',', $datum->mobile);
                foreach ($mobiles as $mobile) {
                    $user = User::find(Mobile::whereMobile($mobile)->first()->user_id);
                    $userInfo = json_decode(Wechat::getUser($token['access_token'], $user->userid));
                    if (!$userInfo->{'errcode'}) {
                        $message = [
                            'touser'  => $user->userid,
                            "msgtype" => "text",
                            "agentid" => $app->agentid,
                            'text'    => [
                                'content' => $datum->content,
                            ],
                        ];
                        $status = json_decode(Wechat::sendMessage($token['access_token'], $message));
                        if ($status->errcode == 0) {
                            $success[] = $mobile;
                        } else {
                            $failure[] = $mobile;
                        }
                    } else {
                        $code = json_encode(
                            Wechat::batchSend(
                                'LKJK004923',
                                "654321@",
                                $mobile,
                                $datum->content . $school->signature
                            )
                        );
                        if ($code != '0' && $code != '-1') {
                            $success[] = $mobile;
                        } else {
                            $failure[] = $mobile;
                        }
                    }
                }
            }
        }
        
        return [
            'message' => sprintf(__('messages.score.message_send_result'), count($success), count($failure)),
            'success' => implode(',', $success),
            'failure' => implode(',', $failure),
        ];
        
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
        $realPath = $file->getRealPath();   //临时文件的绝对路径
        // 上传文件
        $filename = uniqid() . '-' . $file->getClientOriginalName();
        $stored = Storage::disk('uploads')->put(
            date('Y/m/d/', time()) . $filename,
            file_get_contents($realPath)
        );
        abort_if(
            !$stored,
            HttpStatusCode::INTERNAL_SERVER_ERROR,
            __('messages.file_upload_failed')
        );
        $spreadsheet = IOFactory::load(
            $this->uploadedFilePath($filename)
        );
        $scores = $spreadsheet->getActiveSheet()->toArray(
            null, true, true, true
        );
        abort_if(
            !empty(array_diff(self::IMPORT_TITLES, array_values($scores[1]))),
            HttpStatusCode::NOT_ACCEPTABLE,
            __('messages.invalid_file_format')
        );
        # 需要导入成绩的科目
        $titles = $scores[1];
        $subjectNames = [];
        for ($i = ord('D'); $i < ord('D') + sizeof($titles) - 3; $i++) {
            $subjectNames[] = $titles[chr($i)];
        }
        $subjects = Subject::whereIn('name', $subjectNames)
            ->where('school_id', $this->schoolId())->get();
        # 这次考试对应的科目id
        $exam = Exam::find(Request::input('examId'));
        abort_if(
            !$exam,
            HttpStatusCode::NOT_FOUND,
            __('messages.score.exam_not_found')
        );
        $examSubjectIds = explode(',', $exam->subject_ids);
        # 去除表头后的数据
        array_shift($scores);
        # 去除表格的空数据
        foreach ($scores as $key => $value) {
            if ((array_filter($value)) == null) {
                unset($scores[$key]);
            }
        }
        $data = [];
        # 封装需要导入的数据
        foreach ($scores as $score) {
            $basic = [
                'class'          => $score['A'],
                'student_number' => $score['B'],
                'student_name'   => $score['C'],
                'exam_id'        => Request::input('examId'),
            ];
            $index = 'D';
            foreach ($subjects as $subject) {
                if (!in_array($subject->id, $examSubjectIds)) {
                    continue;
                }
                $data[] = array_merge(
                    $basic, [
                        'subject_id' => $subject->id,
                        'score'      => $score[$index],
                    ]
                );
                $index = chr(ord($index) + 1);
            }
        }
        ImportScore::dispatch(
            $data, Auth::id(), Request::input('classId')
        );
        
        return true;
        
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
                $score->student->squad->name,
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
     * 成绩分析
     *
     * @return JsonResponse
     * @throws Throwable
     */
    function stat() {
        
        return response()->json([
            'html' => Request::has('examId')
                ? view('score.class_stat', $this->classStat(false))->render()
                : view('score.student_stat', $this->studentStat())->render(),
        ]);
        
    }
    
    /**
     * 班级成绩分析
     *
     * @param $wechat
     * @return array|bool
     */
    private function classStat($wechat = false) {
        
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
            $subject = Subject::find($sub);
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
    
    /** 微信端 ------------------------------------------------------------------------------------------------------- */
    /**
     * 学生成绩分析
     *
     * @return mixed
     */
    private function studentStat() {
        
        $studentId = Request::input('studentId');
        $classId = Request::input('classId');
        $allowedStudentIds = $this->contactIds('student');
        $allowedClassIds = $this->classIds();
        $student = Student::find($studentId);
        abort_if(
            !in_array($studentId, $allowedStudentIds) || !in_array($classId, $allowedClassIds) || !$student,
            HttpStatusCode::UNAUTHORIZED,
            __('messages.score.unauthorized_stat')
        );
        # 指定学生的最近十场考试
        $exams = Exam::whereRaw('FIND_IN_SET(' . $classId . ', class_ids)')
            ->whereEnabled(1)->orderBy('start_date', 'desc')->take(10)->get();
        $subjectIds = array_unique(
            explode(
                ',', implode(',', $exams->pluck('subject_ids')->toArray())
            )
        );
        $subjects = Subject::whereEnabled(1)
            ->whereIn('id', $subjectIds)
            ->pluck('name', 'id')
            ->toArray();
        ksort($subjects);
        $examScores = [];
        foreach ($exams as $exam) {
            $scores = [];
            foreach ($subjects as $id => $value) {
                $score = $this->where('exam_id', $exam->id)
                    ->where('student_id', $studentId)
                    ->where('enabled', 1)
                    ->where('subject_id', $id)
                    ->first();
                $scores[$id] = [
                    'score'      => $score ? $score->score : '——',
                    'class_rank' => $score ? $score->class_rank : '——',
                    'grade_rank' => $score ? $score->grade_rank : '——',
                ];
            }
            Log::debug(json_encode($scores));
            #处理$studentScore 按照key值升序
            ksort($scores);
            $scoreTotal = ScoreTotal::whereStudentId($studentId)
                ->where('exam_id', $exam->id)->first();
            $examScores[] = [
                'examId'    => $exam->id,
                'examName'  => $exam->name,
                'examTime'  => $exam->start_date,
                'scores'    => $scores,
                'examTotal' => [
                    'score'      => $scoreTotal ? $scoreTotal->score : '——',
                    'class_rank' => $scoreTotal ? $scoreTotal->class_rank : '——',
                    'grade_rank' => $scoreTotal ? $scoreTotal->grade_rank : '——',
                ],
            ];
        }
        
        return [
            'examScores' => $examScores,
            'subjects'   => $subjects,
            'student'    => $student,
        ];
        
    }
    
    /**
     * 返回指定考试/班级对应的班级/学生列表html
     *
     * @return JsonResponse
     */
    function lists() {
        
        $type = Request::input('type');
        $value = Request::input('id');
        
        return response()->json(
            $type == 'class'
                ? (new Exam)->classList($value)
                : (new Squad)->studentList($value)
        );
        
    }
    
    /**
     * 生成指定班级和考试的成绩导入模板
     *
     * @param $examId
     * @param $classId
     * @return bool
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    function template($examId, $classId = null) {
        
        $rows = [];
        $exam = Exam::find($examId);
        $subjects = Subject::whereIn('id', explode(',', $exam->subject_ids))->pluck('name')->toArray();
        $rows[] = array_merge(['班级', '学号', '姓名'], $subjects);
        if (!$classId) {
            $classIds = array_intersect(explode(',', $exam ? $exam->class_ids : ''), $this->classIds());
            $classId = !empty($classIds) ? $classIds[0] : null;
        }
        $class = Squad::find($classId);
        $students = $class ? $class->students : collect([]);
        foreach ($students as $student) {
            $rows[] = [
                $class->name,
                $student->student_number,
                $student->user->realname,
            ];
        }
        
        return $this->excel($rows, 'scores', '成绩导入', false);
        
    }
    
    /**
     * 返回微信端成绩中心首页
     *
     * @return Factory|JsonResponse|View
     * @throws Throwable
     */
    function wIndex() {
        
        $user = Auth::user();
        $schoolId = session('schoolId');
        $pageSize = 4;
        $start = Request::get('start') ? Request::get('start') * $pageSize : 0;
        $exam = new Exam();
        abort_if(
            $user->group->name == '学生',
            HttpStatusCode::UNAUTHORIZED,
            __('messages.unauthorized')
        );
        if (Request::method() == 'POST') {
            $targetId = Request::input('target_id');
            $classId = $user->custodian ? Student::find($targetId)->class_id : $targetId;
            $keyword = Request::has('keyword') ? Request::input('keyword') : null;
            Log::debug('classId: ' . $classId . ' keyword: ' . $keyword);
            $exams = array_slice($exam->examsByClassId($classId, $keyword), $start, $pageSize);
            
            return response()->json([
                'exams' => $exams,
            ]);
        }
        if ($user->custodian) {
            $targets = $user->custodian->myStudents();
            reset($targets);
            $exams = array_slice((new Student)->exams(key($targets)), $start, $pageSize);
        } else {
            $targets = Squad::whereIn('id', $this->classIds($schoolId))
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
     * 返回考试详情数据
     *
     * @return array|Factory|JsonResponse|View|null|string
     */
    function detail() {
        
        $user = Auth::user();
        abort_if(
            $user->group->name == '学生',
            HttpStatusCode::UNAUTHORIZED,
            __('messages.unauthorized')
        );
        
        return Request::has('student')
            ? $this->studentDetail()
            : $this->classDetail();
        
    }
    
    /**
     * 返回指定学生指定考试的分数详情
     *
     * @return Factory|JsonResponse|View
     */
    function studentDetail() {
        
        $total = [];
        if (Request::method() == 'GET') {
            $examId = Request::query('examId');
            $studentId = Request::query('targetId');
        } else {
            $examId = Request::input('examId');
            $studentId = Request::input('studentId');
        }
        $student = Student::find($studentId);
        # 获取该学生所属班级的所有学生
        $exam = Exam::find($examId);
        abort_if(
            !$exam,
            HttpStatusCode::NOT_FOUND,
            __('messages.not_found')
        );
        # 获取该次考试该学生所在的年级id
        $gradeId = $student->squad->grade_id;
        $classIds = Grade::find($gradeId)->classes->pluck('id')->toArray();
        # 获取学生所属班级的所有学生ids
        $classStudentIds = $student->squad->students->pluck('id')->toArray();
        # 获取该年级所有学生
        $gradeStudentIds = Student::whereIn('class_id', $classIds)->get()->pluck('id')->toArray();
        # 获取该次考试所有科目id
        $subjectList = Subject::whereIn('id', explode(',', $exam->subject_ids))->pluck('name', 'id')->toArray();
        if (Request::method() == 'POST') {
            $subjectId = Request::get('subject_id');
        } else {
            reset($subjectList);
            $subjectId = key($subjectList);
        }
        /** @var Score $score */
        $scores = $this->subjectScores($studentId, $subjectId, $examId);
        $score = !$scores->isEmpty() ? $scores->first() : null;
        $allScores = $this->subjectScores($studentId, $subjectId);
        foreach ($allScores as $allScore) {
            $total['names'][] = $allScore->exam->name;
            $total['scores'][] = $allScore->score;
            list($classAvg) = $this->subjectAvg($allScore->exam_id, $subjectId, $classStudentIds);
            $total['avgs'][] = $classAvg;
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
                'exam'  => $exam->toArray()
            ])
            : view('wechat.score.student', [
                'score'     => $score,
                'stat'      => $stat,
                'total'     => $total,
                'subjects'  => $subjectList,
                'exam'      => $exam,
                'studentId' => $studentId,
            ]);
        
    }
    
    /**
     * 查询指定学生某科目全部的考试分数
     *
     * @param $studentId
     * @param $subjectId
     * @param null $examId
     * @return array|Collection|static[]
     */
    private function subjectScores($studentId, $subjectId, $examId = null) {
        
        $conditions = [
            'student_id' => $studentId,
            'subject_id' => $subjectId,
            'enabled'    => 1,
        ];
        
        return $this->where($conditions)->get()->when(
            $examId, function (Collection $scores) use ($examId) {
                return $scores->where('exam_id', $examId);
            }
        );
        
    }
    
    /** Helper functions -------------------------------------------------------------------------------------------- */
    /**
     * 获取学生某次考试在班上的平均分
     *
     * @param $examId
     * @param $subjectId
     * @param $studentIds
     * @return mixed
     */
    private function subjectAvg($examId, $subjectId, array $studentIds) {
        
        $scores = Score::whereExamId($examId)
            ->whereIn('student_id', $studentIds)
            ->where('subject_id', $subjectId)
            ->where('enabled', 1)
            ->get();
        
        return [
            $scores->avg('score') ?? 0,
            $scores->count(),
        ];
        
    }
    
    /**
     * 指定班级的考试详情
     *
     * @return Factory|View
     */
    function classDetail() {
        
        $classId = Request::input('targetId');
        $examId = Request::input('examId');
        $student = Request::input('student');
        if ($classId && $examId) {
            $data = $this->examDetail($examId, $classId, $student);
            
            return view('wechat.score.squad', [
                'data'    => $data,
                'classId' => $classId,
                'examId'  => $examId,
            ]);
        }
        
        return abort(HttpStatusCode::BAD_REQUEST, '请求无效');
        
    }
    
    /**
     * 获取指定考试和班级的指定学生或所有学生的各科目考试详情
     *
     * @param integer $examId
     * @param integer $classId
     * @param null|string $realname - 学生姓名
     * @return array
     */
    private function examDetail($examId, $classId, $realname = null) {
        
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
        foreach ($students as $student) {
            $scores = $this->whereExamId($examId)
                ->where('student_id', $student->id)->get();
            $detail = [];
            foreach ($scores as $score) {
                $detail[] = [
                    'subject' => $score->subject->name,
                    'score'   => $score->score,
                ];
            }
            $result['items'][] = [
                'student_id'     => $student->id,
                'exam_id'        => $examId,
                'realname'       => $student->user['realname'],
                'student_number' => $student->student_number,
                'class_rank'     => 3,
                'grade_rank'     => 5,
                'total'          => 623,
                'detail'         => $detail,
            ];
        }
        
        return $result;
        
    }
    
    /**
     * 返回用于显示指定学生、考试、科目成绩的图表数据
     *
     * @return Factory|JsonResponse|View
     */
    function graph() {
        
        $studentId = Request::input('student_id');
        $examId = Request::input('exam_id');
        $subjectId = Request::input('subject_id');
        if (Request::method() == 'POST') {
            if ($examId && $subjectId) {
                return response()->json(
                    $this->graphData($studentId, $examId, $subjectId)
                );
            }
        }
        $exam = Exam::find($examId);
        $student = Student::find($studentId);
        $subjects = Subject::whereIn('id', explode(',', $exam->subject_ids))->pluck('name', 'id');
        
        return view('wechat.score.graph', [
            'subjects' => $subjects,
            'student'  => $student,
            'exam'     => $exam,
        ]);
        
    }
    
    /**
     * 获取用于显示指定学生指定科目成绩的图标数据
     *
     * @param $studentId
     * @param $examId
     * @param $subjectId
     * @return array
     */
    private function graphData($studentId, $examId, $subjectId) {
        
        $exam = Exam::find($examId);
        if ($subjectId == '-1') {
            $scores = DB::table('score_totals')
                ->join('exams', 'exams.id', '=', 'score_totals.exam_id')
                ->where('student_id', $studentId)
                ->where('exams.start_date', '<=', $exam->start_date)
                ->orderBy('exams.start_date', 'asc')
                ->limit(10)
                ->get();
            $es = [];
            $class_rank = [];
            $grade_rank = [];
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
     * 返回指定班级、考试的成绩分析数据
     *
     * @return Factory|View|string
     */
    function analyze() {
        
        $schoolId = session('schoolId');
        $allowedClassIds = $this->classIds($schoolId);
        $examId = Request::query('examId');
        $classId = Request::query('classId');
        $exam = Exam::find($examId);
        $class = Squad::find($classId);
        abort_if(
            !$exam || !$class || !in_array($classId, $allowedClassIds),
            HttpStatusCode::NOT_FOUND,
            __('messages.not_found')
        );
        $data = $this->classStat(true)
            ?? [
                'className'   => $exam->start_date,
                'examName'    => $exam->name,
                'oneData'     => [],
                'rangs'       => [],
                'totalRanges' => [],
            ];
        
        return view('wechat.score.analyze', [
            'data'    => $data,
            'examId'  => $examId,
            'classId' => $classId,
        ]);
        
    }
    
    /**
     * 成绩综合分析
     *
     * @return Factory|View|string
     */
    function wStat() {
        
        $examId = Request::query('examId');
        $studentId = Request::query('studentId');
        $exam = Exam::find($examId);
        $student = Student::find($studentId);
        abort_if(
            !$student || !$exam,
            HttpStatusCode::NOT_FOUND,
            __('messages.not_found')
        );
        
        return view('wechat.score.stat', [
            'data'      => $this->wAnalyze([
                'exam_id'    => $examId,
                'student_id' => $studentId,
            ]),
            'examName'  => $exam->name,
            'examDate'  => $exam->start_date,
            'studentId' => $studentId,
            'examId'    => $examId,
        ]);
        
    }
    
    /**
     * 返回指定学生指定考试的综合成绩分析数据
     *
     * @param $input
     * @return array|bool
     */
    private function wAnalyze($input) {
        
        $class = Student::find($input['student_id'])->squad;
        # 指定学生/所属班级/所属年级指定考试的所有总分
        /**
         * @var ScoreTotal $scoreTotal
         * @var Collection|ScoreTotal[] $classScoreTotals
         * @var Collection|ScoreTotal[] $gradeScoreTotals
         */
        list($scoreTotal, $classScoreTotals, $gradeScoreTotals) = array_map(
            function (array $studentIds) use ($input) {
                return ScoreTotal::whereEnabled(1)
                    ->whereExamId($input['exam_id'])
                    ->whereIn('student_id', $studentIds)
                    ->get();
            }, [
                [$input['student_id']],
                $class->students->pluck('id')->toArray(),
                $class->grade->students->pluck('id')->toArray(),
            ]
        );
        $data['total'] = [];
        $data['single'] = [];
        # 总分平均分
        $data['total'] = [
            'total_score' => sizeof($scoreTotal) ? $scoreTotal[0]->score : '--',
            'class_avg'   => number_format($classScoreTotals->average('score'), 1),
            'grade_avg'   => number_format($gradeScoreTotals->average('score'), 1),
            'class_rank'  => sizeof($scoreTotal) ? $scoreTotal[0]->class_rank : '--',
            'grade_rank'  => sizeof($scoreTotal) ? $scoreTotal[0]->grade_rank : '--',
            'class_count' => sizeof($scoreTotal) ? $classScoreTotals->count() : '',
            'grade_count' => sizeof($scoreTotal) ? $gradeScoreTotals->count() : '',
        ];
        # 获取指定学生指定考试的各科成绩
        $scores = Score::whereEnabled(1)
            ->where('exam_id', $input['exam_id'])
            ->where('student_id', $input['student_id'])
            ->get();
        foreach ($scores as $score) {
            #获取当前科目下的平均分
            $classScores = Score::whereEnabled(1)
                ->whereIn('student_id', $class->students->pluck('id')->toArray())
                ->where('exam_id', $input['exam_id'])
                ->where('subject_id', $score->subject_id)
                ->get();
            $data['single'][] = [
                'sub'   => $score->subject->name,
                'score' => $score->score,
                'avg'   => number_format($classScores->average('score'), 1),
            ];
            
        }
        
        return $data;
        
    }
    
}
