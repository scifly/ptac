<?php
namespace App\Models;

use App\Facades\Datatable;
use App\Helpers\HttpStatusCode;
use App\Helpers\ModelTrait;
use App\Helpers\Snippet;
use App\Http\Requests\ConsumptionRequest;
use Carbon\Carbon;
use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use Throwable;
use Validator;

/**
 * App\Models\Consumption 消费记录
 *
 * @property int $id
 * @property int $student_id 学生id
 * @property string|null $location 消费地点
 * @property string|null $machineid 消费机id
 * @property int $ctype 消费类型
 * @property float $amount 消费金额
 * @property string $ctime 消费时间
 * @property string $merchant 消费内容
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read \App\Models\Student $student
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
    
    const STAT_RANGE = [
        'student' => 1,
        'class'   => 2,
        'grade'   => 3,
    ];
    const EXPORT_TITLES = ['#', '姓名', '性别', '所属班级', '消费类型', '消费金额', '消费时间', '商品'];
    protected $table = 'consumptions';
    protected $fillable = [
        'student_id', 'location', 'machineid',
        'ctype', 'amount', 'ctime', 'merchant',
    ];
    
    /**
     * 返回消费记录所属的学生对象
     *
     * @return BelongsTo
     */
    function student() {
        
        return $this->belongsTo('App\Models\Student');
        
    }
    
    /**
     * 消费记录列表
     *
     * @return array
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
                    return $d == 0
                        ? sprintf(Snippet::BADGE_GREEN, '充值')
                        : sprintf(Snippet::BADGE_RED, '消费');
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
        $condition = 'Student.id IN (' . implode(',', $this->contactIds('student')) . ')';
        
        return Datatable::simple(
            $this->getModel(), $columns, $joins, $condition
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
                    $student = Student::whereStudentNumber($datum['student_number'])->first();
                    $datum['student_id'] = $student ? $student->id : 0;
                    abort_if(
                        !Validator::make($datum, (new ConsumptionRequest)->rules()),
                        HttpStatusCode::NOT_ACCEPTABLE,
                        __('messages.not_acceptable')
                    );
                    unset($datum['student_number']);
                    $consumptions[] = $datum;
                }
                $this->insert($consumptions);
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return response()->json([
            'statusCode' => HttpStatusCode::OK,
            'message'    => __('messages.ok'),
        ]);
        
    }
    
    /**
     * 删除消费记录
     *
     * @param null $id
     * @return bool|null
     * @throws Exception
     */
    function remove($id = null) {
        
        return $id
            ? $this->find($id)->delete()
            : $this->whereIn('id', array_values(Request::input('ids')))->delete();
        
    }
    
    /**
     * 统计
     *
     * @param array $conditions 统计条件
     * @param null|integer $detail 统计类型，null - 消费/充值总额统计，0 - 消费明细，1 - 充值明细
     * @return array
     */
    function stat(array $conditions, $detail = null) {
        
        list($range, $rangeId) = $this->parseConditions($conditions);
        $values = ['amount', 'ctype'];
        $studentIds = $this->getStudentIds($conditions, $rangeId);
        if (!isset($detail)) {
            $consumption = $charge = 0;
            $amounts = $this->whereIn('student_id', $studentIds)
                ->whereBetween('ctime', $range)
                ->get($values)->toArray();
            foreach ($amounts as $a) {
                if ($a['ctype'] == 0) {
                    $consumption += $a['amount'];
                } else {
                    $charge += $a['amount'];
                }
            }
            
            return [
                '&yen; ' . number_format($consumption, 2),
                '&yen; ' . number_format($charge, 2),
            ];
        }
        $details = [];
        $consumptions = $this->whereIn('student_id', $studentIds)
            ->whereBetween('ctime', $range)
            ->where('ctype', $detail)
            ->get()->toArray();
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
        
        return $details;
        
    }
    
    /**
     * @param array $conditions
     * @return array
     */
    private function parseConditions(array $conditions): array {
        
        $dateRange = explode(' - ', $conditions['date_range']);
        $range = [$dateRange[0] . ' 00:00:00', $dateRange[1] . ' 23:59:59'];
        $rangeId = $conditions['range_id'];
        
        return [$range, $rangeId];
    }
    
    /** Helper functions -------------------------------------------------------------------------------------------- */
    /**
     * 获取需要返回消费记录的学生ids
     *
     * @param array $conditions
     * @param $rangeId
     * @return array
     */
    private function getStudentIds(array $conditions, $rangeId): array {
        
        $studentIds = [];
        switch ($rangeId) {
            case self::STAT_RANGE['student']:
                $studentIds = [$conditions['student_id']];
                break;
            case self::STAT_RANGE['class']:
                $classId = $conditions['class_id'];
                $studentIds = Squad::find($classId)
                    ->students->pluck('id')->toArray();
                break;
            case self::STAT_RANGE['grade']:
                $gradeId = $conditions['grade_id'];
                $studentIds = Grade::find($gradeId)
                    ->students->pluck('id')->toArray();
                break;
            default:
                break;
        }
        
        return $studentIds;
        
    }
    
    /**
     * 批量导出
     *
     * @param null|integer $detail , null - 导出所有记录, 0 - 导出消费明细, 1 - 导出充值明细
     * @param array $conditions
     * @return bool
     * @throws Exception
     */
    function export($detail = null, array $conditions = []) {
        
        if (!isset($detail)) {
            $consumptions = $this->whereIn(
                'student_id',
                $this->contactIds('student')
            )->get();
        } else {
            list($range, $rangeId) = $this->parseConditions($conditions);
            $studentIds = $this->getStudentIds($conditions, $rangeId);
            $consumptions = $this->whereIn('student_id', $studentIds)
                ->whereBetween('ctime', $range)
                ->where('ctype', $detail)
                ->get()->toArray();
        }
        $records[] = self::EXPORT_TITLES;
        /** @var Consumption $c */
        foreach ($consumptions as $c) {
            $records[] = [
                $c->id,
                $c->student->user->realname,
                $c->student->user->gender ? '男' : '女',
                $c->student->squad->name,
                $c->ctype ? '充值' : '消费',
                '&yen; ' . $c->amount,
                $c->ctime,
                $c->merchant,
            ];
        }
        
        return $this->excel($records);
        
    }
    
}
