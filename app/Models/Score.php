<?php
namespace App\Models;

use App\Facades\Datatable;
use App\Helpers\{Constant, HttpStatusCode, ModelTrait, Snippet};
use App\Jobs\{ImportScore, SendMessage};
use Carbon\Carbon;
use Eloquent;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Database\Eloquent\{Builder, Collection, Model, Relations\BelongsTo};
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\{Auth, DB, Request};
use Illuminate\View\View;
use ReflectionClass;
use ReflectionException;
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
 * @property-read Exam $exam
 * @property-read Student $student
 * @property-read Subject $subject
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
 * @method static Builder|Score newModelQuery()
 * @method static Builder|Score newQuery()
 * @method static Builder|Score query()
 * @mixin Eloquent
 */
class Score extends Model {
    
    use ModelTrait;
    
    # 导出格式
    const EXPORT_TITLES = [
        '姓名', '班级', '学号', '考试', '科目', '分数', '班排名', '年排名',
    ];
    
    # 导入格式
    const EXCEL_TITLES = ['班级', '学号', '姓名'];
    
    protected $fillable = [
        'student_id', 'subject_id', 'exam_id',
        'class_rank', 'grade_rank', 'score',
        'enabled',
    ];
    
    /** properties -------------------------------------------------------------------------------------------------- */
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
    
    /** crud -------------------------------------------------------------------------------------------------------- */
    /**
     * 分数记录列表
     *
     * @return array
     */
    function index() {
        
        $columns = [
            ['db' => 'Score.id', 'dt' => 0],
            ['db' => 'User.realname', 'dt' => 1],
            ['db' => 'Student.sn', 'dt' => 2],
            [
                'db'        => 'Grade.id as grade_id', 'dt' => 3,
                'formatter' => function ($d) {
                    return Snippet::icon(Grade::find($d)->name, 'grade');
                },
            ],
            [
                'db'        => 'Squad.id as squad_id', 'dt' => 4,
                'formatter' => function ($d) {
                    return Snippet::icon(Squad::find($d)->name, 'squad');
                },
            ],
            [
                'db'        => 'Score.subject_id', 'dt' => 5,
                'formatter' => function ($d) {
                    return Subject::find($d)->name;
                },
            ],
            [
                'db'        => 'Score.exam_id', 'dt' => 6,
                'formatter' => function ($d) {
                    return Exam::find($d)->name;
                },
            ],
            ['db' => 'Score.score', 'dt' => 7],
            [
                'db'        => 'Score.grade_rank', 'dt' => 9,
                'formatter' => function ($d) {
                    return $d === 0 ? "未统计" : $d;
                },
            ],
            [
                'db'        => 'Score.class_rank', 'dt' => 8,
                'formatter' => function ($d) {
                    return $d === 0 ? "未统计" : $d;
                },
            ],
            ['db' => 'Score.created_at', 'dt' => 10, 'dr' => true],
            ['db' => 'Score.updated_at', 'dt' => 11, 'dr' => true],
            [
                'db'        => 'Score.enabled', 'dt' => 12,
                'formatter' => function ($d, $row) {
                    return Datatable::status($d, $row, false, true, false);
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
            [
                'table'      => 'schools',
                'alias'      => 'School',
                'type'       => 'INNER',
                'conditions' => [
                    'School.id = Grade.school_id',
                ],
            ],
        ];
        $condition = in_array(Auth::user()->role(), Constant::SUPER_ROLES)
            ? 'School.id = ' . $this->schoolId()
            : 'Squad.id IN (' . implode(',', $this->classIds()) . ')';
        
        return Datatable::simple(
            $this, $columns, $joins, $condition
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
     * @throws Throwable
     */
    function remove($id = null) {
        
        return $this->purge(['Score'], 'id', 'purge', $id);
        
    }
    
    /**
     * 发送成绩
     *
     * @param array $data
     * @return bool
     * @throws Throwable
     */
    function send(array $data) {
        
        try {
            DB::transaction(function () use ($data) {
                $school = School::find($this->schoolId());
                $corp = $school->corp;
                $app = $this->app($corp->id);
                $msgType = '成绩消息';
                $msgTypeId = MediaType::whereName('text')->first()->id;
                $mediaTypeId = MediaType::whereName('text')->first()->id;
                $sUserId = Auth::id() ?? 0;
                foreach ($data as $datum) {
                    if (!$datum->mobile) continue;
                    $record = array_combine(Constant::MESSAGE_FIELDS, [
                        0, $mediaTypeId, $app->id, 0, $msgType, '', '0', 0,
                        'http://', '0', $sUserId, 0, $msgTypeId, 0, 0,
                    ]);
                    $record['msl_id'] = MessageSendingLog::create([
                        'read_count'      => 0,
                        'received_count'  => 0,
                        'recipient_count' => 0,
                    ])->id;
                    foreach (explode(',', $datum->mobile) as $mobile) {
                        if (!($m = Mobile::whereMobile($mobile)->first())) continue;
                        $userids[] = User::find($m->user_id)->userid;
                    }
                    $record['content'] = json_encode([
                        'touser'  => implode('|', $userids ?? []),
                        "msgtype" => "text",
                        "agentid" => $app->agentid,
                        'text'    => [
                            'content' => $datum->content,
                        ],
                    ], JSON_UNESCAPED_UNICODE);
                    $messages[] = Message::create($record);
                }
                SendMessage::dispatch($messages ?? [], Auth::id());
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
    /**
     * 针对指定考试进行排名统计
     *
     * @param $examId
     * @return boolean
     * @throws Throwable
     */
    function rank($examId) {
        
        try {
            DB::transaction(function () use ($examId) {
                $exam = Exam::find($examId);
                $subjectIds = explode(',', $exam->subject_ids);
                $classIds = explode(',', $exam->class_ids);
                $gradeIds = array_unique(
                    Squad::whereIn('id', $classIds)->pluck('grade_id')->toArray()
                );
                # 班级/年级id & 学生id对应关系
                list($cSIds, $gSIds) = array_map(function ($name, $ids) {
                    return array_combine($ids, array_map(function ($id) use ($name) {
                        return (new ReflectionClass('App\\Models\\' . ucfirst($name)))
                            ->newInstance()->find($id)->students->pluck('id')->toArray();
                    }, $ids));
                }, ['squad', 'grade'], [$classIds, $gradeIds]);
                # step 1: 单科成绩班级/年级排名
                foreach ($subjectIds as $subjectId) {
                    $this->rankScores([$cSIds, $gSIds], $examId, $subjectId);
                }
                # step 2: 计算学生总成绩
                $scores = Score::whereIn('subject_id', $subjectIds)
                    ->where(['exam_id' => $examId, 'enabled' => 1])
                    ->get()->groupBy('student_id');
                /** @var Score $score */
                foreach ($scores as $score) {
                    $ayeIds = [];
                    $studentId = $score[0]->student_id;
                    $total = $score->sum('score');
                    foreach ($score as $subject) {
                        $ayeIds[] = $subject->subject_id;
                    }
                    # 没有参加的考试科目
                    $nayIds = array_diff($subjectIds, $ayeIds);
                    ScoreTotal::updateOrCreate([
                        'student_id' => $studentId,
                        'exam_id'    => $examId,
                        'enabled'    => 1,
                    ], [
                        'score'          => $total,
                        'subject_ids'    => implode(',', $ayeIds),
                        'na_subject_ids' => implode(',', $nayIds),
                        'class_rank'     => 0,
                        'grade_rank'     => 0,
                    ]);
                }
                # step 3: 总成绩班级/班级排名
                $this->rankScores([$cSIds, $gSIds], $examId);
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
    /**
     * 单科/总成绩排名统计
     *
     * @param array $ids
     * @param integer $examId
     * @param null $subjectId
     */
    private function rankScores(array $ids, $examId, $subjectId = null) {
        
        array_map(
            function ($ids, $field) use ($examId, $subjectId) {
                $className = 'App\\Models\\' . ucfirst($subjectId ? 'score' : 'scoreTotal');
                $model = (new ReflectionClass($className))->newInstance();
                $condition = [
                    ['exam_id', '=', $examId],
                    ['enabled', '=', 1],
                ];
                !$subjectId ?: $condition = array_merge($condition, [['subject_id', '=', $subjectId]]);
                foreach ($ids as $studentIds) {
                    $scores = $model->orderBy('score', 'desc')
                        ->whereIn('student_id', $studentIds)
                        ->where($condition)->get();
                    $tempScore = 0;
                    $rank = 0;
                    foreach ($scores as $score) {
                        if ($tempScore != $score->score) {
                            $tempScore = $score->score;
                            $rank++;
                        }
                        $score->{'update'}([$field => $rank]);
                    }
                }
            }, $ids, ['class_rank', 'grade_rank']
        );
        
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
        
        /**
         * 返回查询条件
         *
         * @param $examId
         * @param $subjectId
         * @param $score
         * @return array
         */
        function condition($examId, $subjectId, $score) {
            
            return [
                ['exam_id', '=', $examId],
                ['subject_id', '=', $subjectId],
                ['enabled', '=', 1],
                ['score', '>=', $score],
            ];
            
        }
        
        #第一个表格数据
        $firstTableData = [];
        #存放满足当前科目的分数段设置和统计人数的数组（第二个表格数据--一个数据一个表格）
        $rangs = [];
        #存放总分分数段设置和统计人数的数组
        $scoreToRanges = [];
        if (
            !($exam = Exam::find(Request::input('examId'))) ||
            !($squad = Squad::find(Request::input('classId')))
        ) {
            return false;
        }
        # 找到班级下面对应所有的学生 ids
        $studentIds = $squad->students->pluck('id')->toArray();
        $builder = Score::whereIn('student_id', $studentIds);
        # 查出当前学校的所有分数段设置
        $srs = ScoreRange::where(['school_id' => $squad->grade->school_id, 'enabled' => 1])->get();
        # 一次处理一个科目  查出这个科目下 班级下所有学生的成绩
        foreach (explode(',', $exam->subject_ids) as $subjectId) {
            $subject = Subject::find($subjectId);
            # 若该学生id没有对应的score则不会在结果数组中
            $condition = condition($exam->id, $subjectId, 0);
            unset($condition[3]);
            $scores = $builder->where($condition)->get();
            $all = $scores->count();
            $avg = $scores->average('score');
            # 大于等于平均分的人数
            $overAvg = $builder->where(condition($exam->id, $subjectId, $avg))->count();
            $firstTableData[] = [
                'sub'        => $subject->name,
                'count'      => $all,
                'max'        => $scores->max('score'),
                'min'        => $scores->min('score'),
                'avg'        => number_format($avg, 2),
                'big_number' => $overAvg,
                'min_number' => $all - $overAvg,
                'subId'      => $subjectId,
            ];
            #处理单科分数段
            foreach ($srs as $sr) {
                #筛选出针对于这一科的所有分数段设置
                if (in_array($subjectId, explode(',', $sr->subject_ids))) {
                    $start = $sr->start_score;
                    $end = $sr->end_score;
                    #需要判断科目满分是否与最大相等
                    $condition = array_merge(
                        condition($exam->id, $subjectId, $start),
                        [['score', $subject->max_score == $end ? '<=' : '<', $end]]
                    );
                    $count = Score::where($condition)->whereIn('student_id', $studentIds)->count();
                    $rangs[$subjectId][] = [
                        'range' => [
                            'min' => $start,
                            'max' => $end,
                        ],
                        'score' => [
                            'sub'    => $subject->name,
                            'count'  => $all,
                            'number' => $count,
                        ],
                    ];
                }
            }
        }
        if (!$wechat) {
            $builder = ScoreTotal::whereIn('student_id', $studentIds);
            #总分分数段
            $trs = $srs->where('subject_ids', 0);
            #查询考试对应的总分 班级
            $condition = [
                ['exam_id', '=', Request::input('examId')],
                ['enabled', '=', 1],
            ];
            #总分总人数
            $totalCount = $builder->where($condition)->count();
            #处理每个总分分数段的人数
            foreach ($trs as $tr) {
                $minTotalRange = $tr->start_score;
                $maxTotalRange = $tr->end_score;
                $condition = array_merge($condition, [
                    [
                        ['score', '>=', $minTotalRange],
                        ['score', '<', $maxTotalRange],
                    ],
                ]);
                $totalNumber = $builder->where($condition)->count();
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
     * @throws ReflectionException
     */
    private function studentStat() {
        
        $studentId = Request::input('studentId');
        $classId = Request::input('classId');
        $student = Student::find($studentId);
        abort_if(
            !in_array($studentId, $this->contactIds('student')) ||
            !in_array($classId, $this->classIds()) || !$student,
            HttpStatusCode::UNAUTHORIZED,
            __('messages.score.unauthorized_stat')
        );
        # 指定学生的最近十场考试
        $exams = Exam::whereRaw('FIND_IN_SET(' . $classId . ', class_ids)')
            ->where('enabled', 1)->orderBy('start_date', 'desc')->take(10)->get();
        $subjectIds = array_unique(
            explode(',', implode(',', $exams->pluck('subject_ids')->toArray()))
        );
        $subjects = Subject::orderBy('id')->where('enabled', 1)
            ->whereIn('id', $subjectIds)->get();
        $examScores = [];
        $placehoders = array_fill(0, 3, '——');
        foreach ($exams as $exam) {
            $scores = [];
            foreach ($subjects->pluck('id')->toArray() as $subjectId) {
                $s = $this->where([
                    'exam_id'    => $exam->id,
                    'student_id' => $studentId,
                    'subject_id' => $subjectId,
                    'enabled'    => 1,
                ])->first();
                $values = $s ? [$s->score, $s->class_rank, $s->grade_rank] : $placehoders;
                $scores[$subjectId] = array_combine(['score', 'class_rank', 'grade_rank'], $values);
            }
            # 处理$studentScore 按照key值升序
            $st = ScoreTotal::where(['student_id' => $studentId, 'exam_id' => $exam->id])->first();
            $values = $st ? [$st->score, $st->class_rank, $st->grade_rank] : $placehoders;
            $examScores[] = [
                'examId'    => $exam->id,
                'examName'  => $exam->name,
                'examTime'  => $exam->start_date,
                'scores'    => $scores ?? [],
                'examTotal' => array_combine(['score', 'class_rank', 'grade_rank'], $values),
            ];
        }
        
        return [
            'examScores' => $examScores,
            'subjects'   => $subjects->pluck('name')->toArray(),
            'student'    => $student,
        ];
        
    }
    
    /**
     * 导入成绩
     *
     * @return bool
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     */
    function import() {
        
        $records = $this->upload(false);
        $titles = $records[0];
        array_shift($records);
        $subjectNames = [];
        for ($i = ord('D'); $i < ord('D') + sizeof($titles) - 3; $i++) {
            $subjectNames[] = $titles[chr($i)];
        }
        $subjects = Subject::whereIn('name', $subjectNames)
            ->where('school_id', $this->schoolId())->get();
        $exam = Exam::find(Request::input('examId'));
        abort_if(
            !$exam,
            HttpStatusCode::NOT_FOUND,
            __('messages.score.exam_not_found')
        );
        $examSubjectIds = explode(',', $exam->subject_ids);
        foreach ($records as $record) {
            $basic = [
                'class'        => $record['A'],
                'sn'           => $record['B'],
                'student_name' => $record['C'],
                'exam_id'      => Request::input('examId'),
            ];
            $index = 'D';
            foreach ($subjects as $subject) {
                if (!in_array($subject->id, $examSubjectIds)) continue;
                $data[] = array_merge(
                    $basic, [
                        'subject_id' => $subject->id,
                        'score'      => floatval($record[$index]),
                    ]
                );
                $index = chr(ord($index) + 1);
            }
        }
        ImportScore::dispatch(
            $data ?? [], Auth::id(), Request::input('classId')
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
        $studentIds = Squad::find($classId)->students->pluck('id')->toArray();
        $scores = $this->whereExamId($examId)->whereIn('student_id', $studentIds)->get();
        $records = [self::EXPORT_TITLES];
        foreach ($scores as $score) {
            if (!$score->student) continue;
            $records[] = [
                $score->student->user->realname,
                $score->student->squad->name,
                $score->student->sn,
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
     * 待发成绩消息预览
     *
     * @return JsonResponse
     */
    function preview() {
        
        $examId = Request::input('examId');
        $classId = Request::input('classId');
        if ($examId && $classId) {
            $result = $this->messages(
                $examId, $classId,
                Request::input('subjectIds') ?? [],
                Request::input('items') ?? []
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
    
    /** 微信端 ------------------------------------------------------------------------------------------------------- */

    /**
     * 返回指定班级参加指定考试的所有学生的单科与总成绩的待发消息
     *
     * @param integer $examId
     * @param integer $classId
     * @param array $subjectIds
     * @param array $options
     * @return array
     */
    private function messages($examId, $classId, array $subjectIds, array $options) {
        
        $stats = [
            'score'      => '',
            'grade_rank' => '年排名',
            'class_rank' => '班排名',
            'grade_avg'  => '年平均',
            'class_avg'  => '班平均',
            'grade_max'  => '年最高',
            'class_max'  => '班最高',
            'grade_min'  => '年最低',
            'class_min'  => '班最低',
        ];
        # 参加指定考试的所有学生id
        $studentIds = Score::whereExamId($examId)->pluck('student_id')->toArray();
        # 指定班级参加指定考试的所有学生
        $cStudents = Student::whereClassId($classId)->get();
        # 指定班级所属年级参加指定考试的所有学生
        $gStudents = Squad::find($classId)->grade->students;
        list($cStudentIds, $gStudentIds) = array_map(
            function (Collection $students) use ($studentIds) {
                return $students->whereIn('id', $studentIds)
                    ->pluck('id')->toArray();
            }, [$cStudents, $gStudents]
        );
        $result = [];
        /** @var Student $student */
        foreach ($cStudents as $student) {
            foreach ($subjectIds as $subjectId) {
                $condition = [
                    'exam_id'    => $examId,
                    'student_id' => $student->id,
                    'subject_id' => $subjectId,
                ];
                $subject = Subject::find($subjectId);
                if ($subject) {
                    $score = Score::where($condition)->first();
                    unset($condition['student_id']);
                    $builder = Score::where($condition);
                } else {
                    unset($condition['subject_id']);
                    $score = ScoreTotal::where($condition)->first();
                    unset($condition['student_id']);
                    $builder = ScoreTotal::where($condition);
                }
                abort_if(
                    !$score, HttpStatusCode::INTERNAL_SERVER_ERROR,
                    __('messages.score.not_found')
                );
                foreach ($options as $option) {
                    $sIds = stripos($option, 'grade') !== false ? $gStudentIds : $cStudentIds;
                    switch ($option) {
                        case 'score':
                        case 'grade_rank':
                        case 'class_rank':
                            $msg = $score->{$option};
                            break;
                        case 'grade_avg':
                        case 'class_avg':
                            $scores = $builder->whereIn('student_id', $sIds)->get();
                            $msg = !$scores->count() ? 0 : $scores->sum('score') / $scores->count();
                            break;
                        case 'grade_max':
                        case 'class_max':
                        case 'grade_min':
                        case 'class_min':
                            $action = substr($option, 6, 3);
                            $msg = $builder->whereIn('student_id', $sIds)->{$action}('score');
                            break;
                        default:
                            break;
                    }
                    $prefix = ($subject ? $subject->name : ($option == 'score' ? '总分' : '')) . ':' . $stats[$option];
                    $message[] = $prefix . ($msg ?? '');
                }
            }
            $studentName = $student->user->realname;
            $content = sprintf(
                __('messages.score.message_template'),
                $studentName, Exam::find($examId)->name,
                implode(',', $message ?? [])
            );
            foreach ($student->custodians as $custodian) {
                $result[] = [
                    'custodian' => $custodian->user->realname,
                    'name'      => $studentName,
                    'mobile'    => $custodian->user->mobiles->where('isdefault', 1)->mobile,
                    'content'   => $content,
                ];
            }
        }
        
        return $result;
        
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
     * 返回指定考试对应的学生及科目列表
     *
     * @param $examId
     * @return array
     */
    function ssList($examId) {
        
        $exam = Exam::find($examId);
        # 指定考试对应的班级
        $classIds = Squad::whereIn('id', explode(',', $exam->class_ids))
            ->where('enabled', 1)->pluck('id')->toArray();
        # 指定考试对应的科目列表
        $subjectList = Subject::whereIn('id', explode(',', $exam->subject_ids))
            ->where('enabled', 1)->pluck('name', 'id')->toArray();
        # 指定考试对应的且对当前用户可见的学生列表
        $students = Student::whereIn('class_id', array_intersect($classIds, $this->classIds()))
            ->where('enabled', 1)->get();
        foreach ($students as $student) {
            $studentList[$student->id] = $student->sn . ' - ' . $student->user->realname;
        }
        
        return response()->json([
            'students' => $this->singleSelectList($studentList ?? [], 'student_id'),
            'subjects' => $this->singleSelectList($subjectList, 'subject_id'),
        ]);
        
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
        
        $exam = Exam::find($examId);
        $subjects = Subject::whereIn('id', explode(',', $exam->subject_ids))
            ->pluck('name')->toArray();
        $rows[] = array_merge(['班级', '学号', '姓名'], $subjects);
        if (!$classId) {
            $classIds = array_intersect(
                explode(',', $exam ? $exam->class_ids : ''),
                $this->classIds()
            );
            $classId = $classIds[0] ?? null;
        }
        $class = Squad::find($classId);
        $students = $class ? $class->students : collect([]);
        foreach ($students as $student) {
            $rows[] = [
                $class->name,
                $student->sn,
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
            $user->role() == '学生',
            HttpStatusCode::UNAUTHORIZED,
            __('messages.unauthorized')
        );
        if (Request::method() == 'POST') {
            $targetId = Request::input('target_id');
            $classId = $user->role() == '监护人' ? Student::find($targetId)->class_id : $targetId;
            $keyword = Request::has('keyword') ? Request::input('keyword') : null;
            $exams = array_slice($exam->examsByClassId($classId, $keyword), $start, $pageSize);
            
            return response()->json(['exams' => $exams]);
        }
        if ($user->role() == '监护人') {
            $targets = $user->custodian->myStudents();
            reset($targets);
            $exams = array_slice((new Student)->exams(key($targets)), $start, $pageSize);
            $type = 'student';
        } else {
            $targets = Squad::whereIn('id', $this->classIds($schoolId))
                ->where('enabled', 1)->pluck('name', 'id')->toArray();
            reset($targets);
            $exams = array_slice($exam->examsByClassId(key($targets)), $start, $pageSize);
            $type = 'class';
        }
        
        return view('wechat.score_center.index', [
            'targets' => $targets,
            'exams'   => $exams,
            'type'    => $type,
        ]);
        
    }
    
    /** Helper functions -------------------------------------------------------------------------------------------- */

    /**
     * 返回考试详情数据
     *
     * @return array|Factory|JsonResponse|View|null|string
     */
    function detail() {
        
        $user = Auth::user();
        abort_if(
            $user->role() == '学生',
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
    private function studentDetail() {
        
        $total = [];
        $examId = Request::query('examId');
        $studentId = Request::query(Request::method() == 'GET' ? 'targetId' : 'studentId');
        $student = Student::find($studentId);
        # 获取该学生所属班级的所有学生
        abort_if(
            !($exam = Exam::find($examId)),
            HttpStatusCode::NOT_FOUND,
            __('messages.not_found')
        );
        # 获取该次考试该学生所在的年级id
        $gradeId = $student->squad->grade_id;
        $classIds = Grade::find($gradeId)->classes->pluck('id')->toArray();
        # 获取学生所属班级的所有学生ids
        $classStudentIds = $student->squad->students->pluck('id')->toArray();
        # 获取该年级所有学生
        $gradeStudentIds = Student::whereIn('class_id', $classIds)->pluck('id')->toArray();
        # 获取该次考试所有科目id
        $subjectList = Subject::whereIn('id', explode(',', $exam->subject_ids))
            ->pluck('name', 'id')->toArray();
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
                'exam'  => $exam->toArray(),
            ])
            : view('wechat.score_center.student', [
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
        
        $condition = [
            'student_id' => $studentId,
            'subject_id' => $subjectId,
            'enabled'    => 1,
        ];
        
        return $this->where($condition)->get()->when($examId,
            function (Collection $scores) use ($examId) {
                return $scores->where('exam_id', $examId);
            }
        );
        
    }
    
    /**
     * 获取学生某次考试在班上的平均分
     *
     * @param $examId
     * @param $subjectId
     * @param $studentIds
     * @return mixed
     */
    private function subjectAvg($examId, $subjectId, array $studentIds) {
        
        $condition = [
            'exam_id'    => $examId,
            'subject_id' => $subjectId,
            'enabled'    => 1,
        ];
        $scores = Score::whereIn('student_id', $studentIds)->where($condition)->get();
        
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
    private function classDetail() {
        
        $classId = Request::input('targetId');
        $examId = Request::input('examId');
        $student = Request::input('student');
        
        return $classId && $examId
            ? view('wechat.score_center.squad', [
                'data'    => $this->examDetail($examId, $classId, $student),
                'classId' => $classId,
                'examId'  => $examId,
            ])
            : abort(HttpStatusCode::BAD_REQUEST, '请求无效');
        
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
                'student_id' => $student->id,
                'exam_id'    => $examId,
                'realname'   => $student->user['realname'],
                'sn'         => $student->sn,
                'class_rank' => 3,
                'grade_rank' => 5,
                'total'      => 623,
                'detail'     => $detail,
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
        $subjectIds = explode(',', $exam->subject_ids);
        $subjects = Subject::whereIn('id', $subjectIds)->pluck('name', 'id');
        
        return view('wechat.score_center.graph', [
            'subjects' => $subjects,
            'student'  => $student,
            'exam'     => $exam,
        ]);
        
    }
    
    /**
     * 获取用于显示指定学生指定科目成绩的图表数据
     *
     * @param $studentId
     * @param $examId
     * @param $subjectId
     * @return array
     */
    private function graphData($studentId, $examId, $subjectId) {
        
        $table = $subjectId == -1 ? 'score_totals' : 'scores';
        $condition = [
            ['student_id', '=', $studentId],
            ['exams.start_date', '<=', Exam::find($examId)->start_date],
        ];
        $table != 'scores'
            ?: $condition = array_merge($condition, ['subject_id', '=', $subjectId]);
        $records = DB::table($table)
            ->join('exams', 'exams.id', '=', $table . '.exam_id')
            ->orderBy('exams.start_date', 'asc')
            ->where($condition)->limit(10)->get();
        foreach ($records as $record) {
            $exams[] = $record->name;
            $class_rank[] = $record->class_rank;
            $grade_rank[] = $record->grade_rank;
        }
        
        return [
            'exam'       => $exams ?? [],
            'class_rank' => $class_rank ?? [],
            'grade_rank' => $grade_rank ?? [],
        ];
        
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
        
        return view('wechat.score_center.analyze', [
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
        
        return view('wechat.score_center.stat', [
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
        
        $fields = ['exam_id', 'student_id', 'enabled'];
        $class = Student::find($input['student_id'])->squad;
        # 指定学生/所属班级/所属年级指定考试的所有总分
        /**
         * @var Collection|ScoreTotal[] $STs
         * @var Collection|ScoreTotal[] $cSTs
         * @var Collection|ScoreTotal[] $gSTs
         */
        list($STs, $cSTs, $gSTs) = array_map(
            function (array $studentIds) use ($input) {
                return ScoreTotal::where(['enabled' => 1, 'exam_id' => $input['exam_id']])
                    ->whereIn('student_id', $studentIds)->get();
            }, [
                [$input['student_id']],
                $class->students->pluck('id')->toArray(),
                $class->grade->students->pluck('id')->toArray(),
            ]
        );
        list($cAvg, $gAvg) = array_map(
            function (Collection $sts) {
                return number_format($sts->average('score'), 1);
            }, [$cSTs, $gSTs]
        );
        $st = $STs->first();
        $data['total'] = [
            'total_score' => $st ? $st->score : '--',
            'class_avg'   => $cAvg,
            'grade_avg'   => $gAvg,
            'class_rank'  => $st ? $st->class_rank : '--',
            'grade_rank'  => $st ? $st->grade_rank : '--',
            'class_count' => $st ? $cSTs->count() : '',
            'grade_count' => $st ? $gSTs->count() : '',
        ];
        # 获取指定学生指定考试的各科成绩
        $scores = Score::where(array_combine(
            $fields, [$input['exam_id'], $input['student_id'], 1]
        ))->get();
        foreach ($scores as $score) {
            $studentIds = $class->students->pluck('id')->toArray();
            $cScores = Score::where(array_combine(
                $fields, [$input['exam_id'], $score->subject_id, 1]
            ))->whereIn('student_id', $studentIds)->get();
            $data['single'][] = [
                'sub'   => $score->subject->name,
                'score' => $score->score,
                'avg'   => number_format($cScores->average('score'), 1),
            ];
        }
        
        return $data;
        
    }
    
}
