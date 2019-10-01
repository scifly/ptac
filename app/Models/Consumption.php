<?php
namespace App\Models;

use App\Facades\Datatable;
use App\Helpers\{Constant, ModelTrait};
use App\Http\Requests\ConsumptionRequest;
use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\{Builder, Model, Relations\BelongsTo};
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\{DB, Request};
use ReflectionException;
use Throwable;
use Validator;

/**
 * App\Models\Consumption 消费记录
 *
 * @property int $id
 * @property int $student_id 学生id
 * @property string|null $location 消费地点
 * @property string|null $machineid 消费机id
 * @property int $ctype 消费类型，0：充值，1：消费
 * @property float $amount 消费金额
 * @property string $ctime 消费时间
 * @property string $merchant 消费内容
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Student $student
 * @method static Builder|Consumption newModelQuery()
 * @method static Builder|Consumption newQuery()
 * @method static Builder|Consumption query()
 * @method static Builder|Consumption whereAmount($value)
 * @method static Builder|Consumption whereCreatedAt($value)
 * @method static Builder|Consumption whereCtime($value)
 * @method static Builder|Consumption whereCtype($value)
 * @method static Builder|Consumption whereId($value)
 * @method static Builder|Consumption whereLocation($value)
 * @method static Builder|Consumption whereMachineid($value)
 * @method static Builder|Consumption whereMerchant($value)
 * @method static Builder|Consumption whereStudentId($value)
 * @method static Builder|Consumption whereUpdatedAt($value)
 * @mixin Eloquent
 */
class Consumption extends Model {
    
    use ModelTrait;
    
    const EXPORT_TITLES = ['#', '姓名', '性别', '所属班级', '消费类型', '消费金额', '消费时间', '商品'];
    
    protected $fillable = [
        'student_id', 'location', 'machineid',
        'ctype', 'amount', 'ctime', 'merchant',
    ];
    
    /** @return BelongsTo */
    function student() { return $this->belongsTo('App\Models\Student'); }
    
    /**
     * 消费记录列表
     *
     * @return array
     * @throws ReflectionException
     */
    function index() {
        
        $columns = [
            ['db' => 'Consumption.id', 'dt' => 0],
            ['db' => 'User.realname', 'dt' => 1],
            ['db' => 'Consumption.location', 'dt' => 2],
            ['db' => 'Consumption.machineid', 'dt' => 3],
            [
                'db'        => 'Consumption.ctype', 'dt' => 4,
                'formatter' => function ($d) {
                    return $this->badge(
                        $d ? 'text-red' : 'text-green',
                        $d ? '消费' : '充值'
                    );
                },
            ],
            [
                'db'        => 'Consumption.amount', 'dt' => 5,
                'formatter' => function ($d) {
                    setlocale(LC_MONETARY, 'zh_CN.UTF-8');
                    
                    return money_format('%.2n', $d);
                },
            ],
            ['db' => 'Consumption.ctime', 'dt' => 6],
        ];
        $joins = [
            [
                'table'      => 'students',
                'alias'      => 'Student',
                'type'       => 'INNER',
                'conditions' => [
                    'Student.id = Consumption.student_id',
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
        ];
        $condition = 'Student.id IN (' . $this->contactIds('student')->join(',') . ')';
        
        return Datatable::simple(
            $this, $columns, $joins, $condition
        );
        
    }
    
    /**
     * 保存消费记录
     *
     * @return bool
     * @throws Throwable
     */
    function store() {
        
        try {
            DB::transaction(function () {
                $post = json_decode(Request::getContent(), true);
                $data = $post['data'];
                $consumptions = [];
                foreach ($data as &$datum) {
                    $student = Student::whereSn($datum['sn'])->first();
                    $datum['student_id'] = $student ? $student->id : 0;
                    throw_if(
                        !Validator::make($datum, (new ConsumptionRequest)->rules()),
                        new Exception(__('messages.not_acceptable'))
                    );
                    unset($datum['sn']);
                    $consumptions[] = $datum;
                }
                $this->insert($consumptions);
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return response()->json([
            'statusCode' => Constant::OK,
            'message'    => __('messages.ok'),
        ]);
        
    }
    
    /**
     * 删除消费记录
     *
     * @param null $id
     * @return bool|null
     * @throws Throwable
     */
    function remove($id = null) {
        
        return $this->purge($id);
        
    }
    
    /**
     * 统计
     *
     * @return array
     */
    function stat() {
        
        [$range, $rangeId] = $this->parse();
        $values = ['amount', 'ctype'];
        $studentIds = $this->studentIds($rangeId);
        if (!$detail = Request::query('detail')) {
            $consumption = $charge = 0;
            $amounts = $this->whereIn('student_id', $studentIds)
                ->whereBetween('ctime', $range)->get($values);
            foreach ($amounts as $a) {
                if ($a['ctype'] == 0) {
                    $consumption += $a['amount'];
                } else {
                    $charge += $a['amount'];
                }
            }
            
            return [
                'consumption' => '&yen; ' . number_format($consumption, 2),
                'charge'      => '&yen; ' . number_format($charge, 2),
            ];
        }
        $details = [];
        $consumptions = $this->whereIn('student_id', $studentIds)
            ->whereBetween('ctime', $range)
            ->where('ctype', $detail)->get();
        foreach ($consumptions as $c) {
            $details[] = [
                'id'        => $c->id,
                'name'      => $c->student->user->realname,
                'amount'    => '&yen; ' . number_format($c->amount, 2),
                'type'      => $c->ctype ? '充值' : '消费',
                'machineid' => $c->machineid,
                'datetime'  => $c->ctime,
                'location'  => $c->location,
            ];
        }
        
        return ['details' => $details];
        
    }
    
    /** @return array */
    private function parse(): array {
        
        $conditions = Request::all();
        $dateRange = explode(' - ', $conditions['date_range']);
        $range = [$dateRange[0] . ' 00:00:00', $dateRange[1] . ' 23:59:59'];
        $rangeId = $conditions['range_id'];
        
        return [$range, $rangeId];
        
    }
    
    /** Helper functions -------------------------------------------------------------------------------------------- */
    /**
     * 获取需要返回消费记录的学生ids
     *
     * @param $rangeId
     * @return array
     */
    function studentIds($rangeId) {
        
        $input = Request::all();
        if ($rangeId == 1) {
            $students = Student::whereId($input['student_id'])->get();
        } elseif ($rangeId == 2) {
            $students = Squad::find($input['class_id'])->students;
        } else {
            $students = Grade::find($input['grade_id'])->students;
        }
        
        return $students->pluck('id');
        
    }
    
    /**
     * 批量导出
     *
     * @return bool
     * @throws Exception
     */
    function export() {
        
        if (!$detail = Request::query('detail')) {
            $consumptions = $this->whereIn(
                'student_id',
                $this->contactIds('student')
            )->get();
        } else {
            [$range, $rangeId] = $this->parse();
            $studentIds = $this->studentIds($rangeId);
            $consumptions = $this->whereIn('student_id', $studentIds)
                ->whereBetween('ctime', $range)
                ->where('ctype', $detail)->get();
        }
        $records[] = self::EXPORT_TITLES;
        /** @var Consumption $c */
        foreach ($consumptions as $c) {
            $student = $c->student;
            $user = $student->user;
            $records[] = [
                $c->id,
                $user->realname,
                $user->gender ? '男' : '女',
                $student->squad->name,
                $c->ctype ? '充值' : '消费',
                '&yen; ' . $c->amount,
                $c->ctime,
                $c->merchant,
            ];
        }
        
        return $this->excel($records);
        
    }
    
    /**
     * @return array
     * @throws ReflectionException
     * @throws Exception
     */
    function compose() {
    
        $action = explode('/', Request::path())[1];
        if ($action == 'index') {
            $data = [
                'buttons' => [
                    'stat'   => [
                        'id'    => 'stat',
                        'label' => '统计',
                        'icon'  => 'fa fa-bar-chart',
                    ],
                    'export' => [
                        'id'    => 'export',
                        'label' => '批量导出',
                        'icon'  => 'fa fa-download',
                    ],
                ],
                'titles'  => ['#', '学生', '消费地点', '消费机ID', '类型', '金额', '时间'],
            ];
        } else {
            $values = Student::whereIn('id', $this->contactIds('student'))->get();
            foreach ($values as $v) {
                $students[$v->id] = $v->user->realname . '(' . $v->squad->grade->name . ' / ' . $v->squad->name . ')';
            }
            [$classes, $grades] = array_map(
                function (Builder $name, $data) {
                    return $name::{'whereIn'}('id', $data)->pluck('name', 'id');
                }, ['Squad', 'Grade'], [$this->classIds(), $this->gradeIds()]
            );
            $data = [
                'ranges'   => [1 => '学生', 2 => '班级', 3 => '年级'],
                'students' => $students ?? [],
                'classes'  => $classes,
                'grades'   => $grades,
            ];
        }
        
        return $data;
        
    }
    
}
