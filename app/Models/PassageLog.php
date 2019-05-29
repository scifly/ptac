<?php
namespace App\Models;

use App\Facades\Datatable;
use App\Helpers\ModelTrait;
use App\Helpers\Snippet;
use App\Jobs\GatherPassageLog;
use Eloquent;
use Illuminate\Database\Eloquent\{Builder, Model, Relations\BelongsTo};
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Throwable;

/**
 * 通行记录
 *
 * Class PassageLog
 *
 * @package App\Models
 * @property int $id
 * @property int $school_id 学校id
 * @property int $user_id 用户id
 * @property int $category 记录类型
 * @property int $direction 进出方向
 * @property int $turnstile_id 门禁id
 * @property int $door 通行门编号: 1 - 4
 * @property string $clocked_at 打卡时间
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $status 通行状态
 * @property-read School $school
 * @property-read Turnstile $turnstile
 * @property-read User $user
 * @method static Builder|PassageLog newModelQuery()
 * @method static Builder|PassageLog newQuery()
 * @method static Builder|PassageLog query()
 * @method static Builder|PassageLog whereClockedAt($value)
 * @method static Builder|PassageLog whereCreatedAt($value)
 * @method static Builder|PassageLog whereDirection($value)
 * @method static Builder|PassageLog whereDoor($value)
 * @method static Builder|PassageLog whereId($value)
 * @method static Builder|PassageLog whereCategory($value)
 * @method static Builder|PassageLog whereSchoolId($value)
 * @method static Builder|PassageLog whereStatus($value)
 * @method static Builder|PassageLog whereTurnstileId($value)
 * @method static Builder|PassageLog whereUpdatedAt($value)
 * @method static Builder|PassageLog whereUserId($value)
 * @mixin Eloquent
 */
class PassageLog extends Model {
    
    use ModelTrait;
    
    protected $table = 'passage_logs';
    
    protected $fillable = [
        'school_id', 'user_id', 'category',
        'direction', 'turnsitle_id', 'door',
        'clocked_at', 'status',
    ];
    
    /**
     * 返回通行记录所属的学校对象
     *
     * @return BelongsTo
     */
    function school() {
        return $this->belongsTo('App\Models\School');
    }
    
    /**
     * 返回通行记录所属的用户对象
     *
     * @return BelongsTo
     */
    function user() {
        return $this->belongsTo('App\Models\User');
    }
    
    /**
     * 返回通行记录所属的门禁对象
     *
     * @return BelongsTo
     */
    function turnstile() {
        return $this->belongsTo('App\Models\Turnstile');
    }
    
    /**
     * 门禁通行记录列表
     *
     * @return array
     */
    function index() {
        
        $columns = [
            ['db' => 'PassageLog.id', 'dt' => 0],
            ['db' => 'User.realname', 'dt' => 1],
            ['db' => 'Groups.name as role', 'dt' => 2],
            [
                'db'        => 'PassageLog.category', 'dt' => 3,
                'formatter' => function ($d) {
                    $categories = [
                        '无记录', '刷卡记录', '门磁', '报警记录',
                    ];
                    
                    return $categories[$d];
                },
            ],
            [
                'db'        => 'PassageLog.direction', 'dt' => 4,
                'formatter' => function ($d) {
                    return sprintf(
                        Snippet::BADGE,
                        $d ? 'text-green' : 'text-red',
                        $d ? '进' : '出'
                    );
                },
            ],
            ['db' => 'Turnstile.location', 'dt' => 5],
            ['db' => 'PassageLog.clocked_at', 'dt' => 6, 'dr' => true],
            [
                'db'        => 'PassageLog.status', 'dt' => 7,
                'formatter' => function ($d, $row) {
                    return $row['role'] == '监护人'
                        ? ' - '
                        : sprintf(
                            Snippet::BADGE,
                            $d ? 'text-green' : 'text-red',
                            $d ? '正常' : '异常'
                        );
                },
            ],
        ];
        $joins = [
            [
                'table'      => 'users',
                'alias'      => 'User',
                'type'       => 'INNER',
                'conditions' => [
                    'User.id = PassageLog.user_id',
                ],
            ],
            [
                'table'      => 'groups',
                'alias'      => 'Groups',
                'type'       => 'INNER',
                'conditions' => [
                    'Groups.id = User.group_id',
                ],
            ],
            [
                'table'      => 'turnstiles',
                'alias'      => 'Turnstile',
                'type'       => 'INNER',
                'conditions' => [
                    'Turnstile.id = PassageLog.turnstile_id',
                ],
            ],
        ];
        $condition = 'PassageLog.school_id = ' . $this->schoolId();
        
        return Datatable::simple(
            $this, $columns, $joins, $condition
        );
        
    }
    
    /**
     * 采集门禁通行记录
     *
     * @return bool
     * @throws Throwable
     */
    function store() {
        
        GatherPassageLog::dispatch(
            $this->schoolId(), Auth::id()
        );
        
        return true;
        
    }
    
    /**
     * 批量导出记录
     *
     * @return string
     */
    function export() {
        
        return '下载地址';
        
    }
    
}
