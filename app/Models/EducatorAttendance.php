<?php
namespace App\Models;

use App\Facades\Datatable;
use App\Helpers\{HttpStatusCode, ModelTrait, Snippet};
use App\Http\Requests\EducatorAttendanceRequest;
use Carbon\Carbon;
use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\{Builder, Model, Relations\BelongsTo};
use Illuminate\Support\Facades\{DB, Request, Session};
use ReflectionException;
use Throwable;
use Validator;

/**
 * App\Models\EducatorAttendance 教职员工考勤记录
 *
 * @property int $id
 * @property int $educator_id 教职员工ID
 * @property string $clocked_at 打卡日期时间
 * @property float $longitude 签到时所处经度
 * @property float $latitude 签到时所处纬度
 * @property int $direction 进或出
 * @property int $eas_id 所属考勤设置ID
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $status 考勤状态
 * @property-read EducatorAppeal $educatorAppeal
 * @property-read EducatorAttendanceSetting $educatorAttendanceSetting
 * @property-read Educator $educator
 * @method static Builder|EducatorAttendance whereCreatedAt($value)
 * @method static Builder|EducatorAttendance whereEasId($value)
 * @method static Builder|EducatorAttendance whereEducatorId($value)
 * @method static Builder|EducatorAttendance whereId($value)
 * @method static Builder|EducatorAttendance whereDirection($value)
 * @method static Builder|EducatorAttendance whereLatitude($value)
 * @method static Builder|EducatorAttendance whereLongitude($value)
 * @method static Builder|EducatorAttendance whereClockedAt($value)
 * @method static Builder|EducatorAttendance whereUpdatedAt($value)
 * @method static Builder|EducatorAttendance whereStatus($value)
 * @method static Builder|EducatorAttendance newModelQuery()
 * @method static Builder|EducatorAttendance newQuery()
 * @method static Builder|EducatorAttendance query()
 * @mixin Eloquent
 */
class EducatorAttendance extends Model {
    
    use ModelTrait;
    
    const EXPORT_TITLES = [
        '姓名', '手机号码', '打卡时间', '进/出', '状态',
    ];
    protected $table = 'educator_attendances';
    protected $fillable = [
        'educator_id', 'clocked_at', 'longitude',
        'latitude', 'direction', 'eas_id',
    ];
    
    /**
     * 获取对应的教职员工对象
     *
     * @return BelongsTo
     */
    function educator() { return $this->belongsTo('App\Models\Educator'); }
    
    /**
     * 获取对应的教职员工考勤设置对象
     *
     * @return BelongsTo
     */
    function educatorAttendanceSetting() {
        
        return $this->belongsTo('App\Models\EducatorAttendanceSetting', 'eas_id');
        
    }
    
    /**
     * 教职员工考勤记录列表
     *
     * @return array
     * @throws ReflectionException
     */
    function index() {
        
        $columns = [
            ['db' => 'EducatorAttendance.id', 'dt' => 0],
            ['db' => 'User.realname', 'dt' => 1],
            ['db' => 'EducatorAttendance.clocked_at', 'dt' => 2, 'dr' => true],
            [
                'db'        => 'EducatorAttendance.direction', 'dt' => 3,
                'formatter' => function ($d) {
                    return sprintf(
                        Snippet::BADGE,
                        $d ? 'text-green' : 'text-red',
                        $d ? '进' : '出'
                    );
                },
            ],
            ['db' => 'EducatorAttendanceSetting.name', 'dt' => 4],
            [
                'db'        => 'EducatorAttendance.status', 'dt' => 5,
                'formatter' => function ($d) {
                    return sprintf(
                        Snippet::BADGE,
                        $d ? 'text-green' : 'text-red',
                        $d ? '正常' : '异常'
                    );
                },
            ],
        ];
        $joins = [
            [
                'table'      => 'educators',
                'alias'      => 'Educator',
                'type'       => 'INNER',
                'conditions' => [
                    'Educator.id = EducatorAttendance.educator_id',
                ],
            ],
            [
                'table'      => 'users',
                'alias'      => 'User',
                'type'       => 'INNER',
                'conditions' => [
                    'User.id = Educator.user_id',
                ],
            ],
            [
                'table'      => 'educator_attendance_settings',
                'alias'      => 'EducatorAttendanceSetting',
                'type'       => 'INNER',
                'conditions' => [
                    'EducatorAttendanceSetting.id = EducatorAttendance.eas_id',
                ],
            ],
        ];
        $condition = 'EducatorAttendance.educator_id IN(' .
            implode(',', $this->contactIds('educator')) . ')';
        
        return Datatable::simple(
            $this->getModel(), $columns, $joins, $condition
        );
        
    }
    
    /**
     * 保存教职员工考勤记录
     *
     * @return bool
     * @throws Throwable
     */
    function store() {
        
        try {
            DB::transaction(function () {
                $data = Request::input('data');
                $eas = [];
                foreach ($data as &$datum) {
                    $datum['longitude'] = $datum['longitude'] ?? 0;
                    $datum['latitude'] = $datum['latitude'] ?? 0;
                    abort_if(
                        !Validator::make($datum, (new EducatorAttendanceRequest)->rules()),
                        HttpStatusCode::NOT_ACCEPTABLE,
                        __('messages.not_acceptable')
                    );
                    $user = User::find($datum['user_id']);
                    abort_if(
                        !$user,
                        HttpStatusCode::NOT_FOUND,
                        __('messages.user_not_found')
                    );
                    abort_if(
                        !($educator = $user->educator),
                        HttpStatusCode::NOT_FOUND,
                        __('messages.educator_not_found')
                    );
                    $dateTime = $datum['clocked_at'];
                    $clockedAt = date('H:i:s', strtotime($dateTime));
                    $eases = EducatorAttendanceSetting::whereSchoolId($educator->school_id)
                        ->where('enabled', 1)->get();
                    $status = 0; # 考勤异常
                    $easId = 0;
                    foreach ($eases as $eas) {
                        $easId = $eas->id;
                        if ($clockedAt <= $eas->end && $clockedAt >= $eas->start) {
                            $status = 1;
                            break;
                        }
                    }
                    $eas[] = [
                        'educator_id' => $educator->id,
                        'clocked_at'  => $dateTime,
                        'longitude'   => $datum['longitude'],
                        'latitude'    => $datum['latitude'],
                        'direction'   => $datum['direction'],
                        'eas_id'      => $easId,
                        'status'      => $status,
                    ];
                }
                $this->insert($eas);
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
     * 删除教职员工考勤记录
     *
     * @param null $id
     * @return bool
     * @throws Throwable
     */
    function remove($id = null) {
    
        try {
            DB::transaction(function () use ($id) {
                $ids = $id ? [$id] : array_values(Request::input('ids'));
                $this->purge(['EducatorAppeal'], 'ea_ids', 'clear', $ids);
                Request::replace(['ids' => $ids]);
                $this->purge([class_basename($this)], 'id');
            });
        } catch (Exception $e) {
            throw $e;
        }
    
        return true;
        
    }
    
    /**
     * 考勤统计
     *
     * @return array
     * @throws ReflectionException
     */
    function stat() {
        
        Request::validate([
            'start_date' => 'required|date',
            'end_date'   => 'required|date|greater_than_or_equal_to:start_date',
        ]);
        $attendances = $this->latestAttendances(
            implode(',', $this->contactIds('educator')),
            Request::input('start_date'),
            Request::input('end_date')
        );
        $startDate = Carbon::createFromTimestamp(strtotime(Request::input('start_date')));
        $endDate = Carbon::createFromTimestamp(strtotime(Request::input('end_date')));
        $normals = $abnormals = [];
        if (!empty($attendances)) {
            foreach ($attendances as $key => &$val) {
                if ($val->latest) {
                    if (isset($normals[$val->day])) {
                        $normals[$val->day] += 1;
                    } else {
                        $normals[$val->day] = 1;
                    }
                } else {
                    if (isset($abnormals[$val->day])) {
                        $abnormals[$val->day] += 1;
                    } else {
                        $abnormals[$val->day] = 1;
                    }
                }
            }
        }
        $results = [];
        for ($i = 0; $i < $startDate->diffInDays($endDate) + 1; $i++) {
            $date = $startDate->addDay($i)->toDateString();
            $all = sizeof($this->contactIds('educator'));
            $normal = $normals[$date] ?? 0;
            $abnormal = $abnormals[$date] ?? 0;
            $results[$i] = [
                'date'     => $date,
                'all'      => $all,
                'normal'   => $normal,
                'abnormal' => $abnormal,
                'missed'   => $all - $normal - $abnormal,
            ];
        }
        
        return $results;
        
    }
    
    /**
     * 获取考勤数据
     *
     * @param $educatorIds
     * @param $start
     * @param $end
     * @return array
     */
    private function latestAttendances($educatorIds, $start, $end) {
        
        return DB::select("
            SELECT
                MAX(t.id) id,
                t.educator_id,
                SUBSTRING_INDEX(GROUP_CONCAT(t.status ORDER BY t.clocked_at DESC), ',', 1) latest,
                DATE(t.clocked_at) day
            FROM (
                SELECT
                    ea.id,
                    ea.educator_id,
                    ea.direction,
                    ea.clocked_at,
                    ea.status
                FROM
                    educator_attendances ea
                WHERE
                    ea.clocked_at >= '" . $start . "' AND
                    ea.clocked_at <= '" . $end . "'
                ORDER BY
                    ea.educator_id ASC,
                    ea.clocked_at DESC
            ) t
            WHERE
                t.educator_id IN (" . $educatorIds . ")
            GROUP BY
                t.educator_id,
                day
        ");
        
    }
    
    /**
     * 获取考勤明细
     *
     * @return mixed
     * @throws ReflectionException
     */
    function detail() {
        
        $details = $this->details();
        # 缓存导出数据
        $this->cache($details);
        
        return $details;
        
    }
    
    /** Helper functions -------------------------------------------------------------------------------------------- */
    /**
     * @return array
     * @throws ReflectionException
     */
    private function details(): array {
        
        $date = Request::input('date');
        $type = Request::input('type');
        $startTime = date('Y-m-d H:i:s', strtotime($date));
        $endTime = date('Y-m-d H:i:s', strtotime($date) + 24 * 3600 - 1);
        $educatorIds = $this->contactIds('educator'); # 对当前用户可见的所有教职员工id
        $results = []; # 统计结果
        if ($type == 'missed') {
            # 打过考勤的教职员工ids
            $nEducatorIds = $this->whereIn('educator_id', $educatorIds)
                ->whereBetween('clocked_at', [$startTime, $endTime])
                ->get()->pluck('educator_id')->toArray();
            # 未打考勤的教职员工
            $mEducators = Educator::whereIn('id', array_diff($educatorIds, $nEducatorIds))->get();
            if ($mEducators) {
                foreach ($mEducators as $educator) {
                    if (json_decode($educator->user['mobiles'])) {
                        $mobiles = array_column(
                            json_decode($educator->user['mobiles']),
                            'mobile'
                        );
                    } else {
                        $mobiles = [];
                    }
                    $results[] = [
                        'name'       => $educator->user['realname'],
                        'mobile'     => $mobiles,
                        'clocked_at' => '',
                        'direction'  => '',
                        'status'     => '未打',
                    ];
                }
            }
        }
        $attendances = $this->latestAttendances(
            implode(',', $educatorIds),
            $startTime,
            $endTime
        );
        if ($attendances) {
            switch ($type) {
                case 'normal':
                    $eaIds = [];
                    foreach ($attendances as $a) {
                        !$a->latest ?: $eaIds[] = $a->id;
                    }
                    $eas = $this->whereIn('id', $eaIds)->get();
                    foreach ($eas as $ea) {
                        $results[] = [
                            'name'       => $ea->educator->user->realname,
                            'mobile'     => array_column($ea->educator->user->mobiles->toArray(), 'mobile'),
                            'clocked_at' => $ea->clocked_at,
                            'direction'  => $ea->direction == 1 ? '进' : '出',
                            'status'     => '正常',
                        ];
                    }
                    break;
                case 'abnormal':
                    $eaIds = [];
                    foreach ($attendances as $a) {
                        $a->latest != 0 ?: $eaIds = $a->id;
                    }
                    $eas = $this->whereIn('id', $eaIds)->get();
                    foreach ($eas as $ea) {
                        $results[] = [
                            'name'       => $ea->educator->user->realname,
                            'mobile'     => array_column($ea->student->user->mobiles->toArray(), 'mobile'),
                            'clocked_at' => $ea->clocked_at,
                            'direction'  => $ea->direction ? '进' : '出',
                            'status'     => '异常',
                        ];
                    }
                    break;
                default:
                    break;
            }
        }
        
        return $results;
        
    }
    
    /**
     * 缓存导出数据
     *
     * @param $details
     */
    private function cache($details): void {
        
        $rows = [self::EXPORT_TITLES];
        foreach ($details as $detail) {
            $rows[] = [
                $detail['name'],
                implode(',', $detail['mobile']),
                $detail['clocked_at'],
                $detail['direction'] ? '进' : '出',
                $detail['status'] == 0 ? '异常' : ($detail['status'] == 1 ? '正常' : '未打'),
            ];
        }
        session(['ea_details' => $rows]);
        
    }
    
    /**
     * 导出教职员工考勤明细
     *
     * @return mixed
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    function export() {
        
        abort_if(
            !session('ea_details'),
            HttpStatusCode::BAD_REQUEST,
            __('messages.bad_request')
        );
        $details = session('ea_details');
        Session::forget('ea_details');
        
        return $this->excel(
            $details,
            '教职员工考勤明细',
            '考勤明细'
        );
        
    }
    
    /**
     * 删除指定教职员工的考勤记录
     *
     * @param $educatorId
     * @throws Exception
     */
    function removeEducator($educatorId) {
        
        $this->whereEducatorId($educatorId)->delete();
        
    }
    
}
