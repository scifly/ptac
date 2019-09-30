<?php
namespace App\Models;

use App\Facades\Datatable;
use App\Helpers\ModelTrait;
use Eloquent;
use Html;
use Illuminate\Database\Eloquent\{Builder, Model, Relations\BelongsTo};
use Illuminate\Support\{Carbon, Facades\Auth};
use Request;
use Throwable;

/**
 * App\Models\Flow 审批流程日志
 *
 * @property int $id
 * @property int $flow_type_id 流程类型ID
 * @property int $user_id 发起人用户ID
 * @property int $media_id 媒体ID
 * @property mixed|null $logs 审批日志
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $enabled
 * @property-read FlowType $flowType
 * @property-read Media $media
 * @property-read User $user
 * @method static Builder|Flow newModelQuery()
 * @method static Builder|Flow newQuery()
 * @method static Builder|Flow query()
 * @method static Builder|Flow whereCreatedAt($value)
 * @method static Builder|Flow whereEnabled($value)
 * @method static Builder|Flow whereFlowTypeId($value)
 * @method static Builder|Flow whereId($value)
 * @method static Builder|Flow whereLogs($value)
 * @method static Builder|Flow whereUpdatedAt($value)
 * @method static Builder|Flow whereUserId($value)
 * @mixin Eloquent
 * @method static Builder|Flow whereMediaId($value)
 */
class Flow extends Model {
    
    use ModelTrait;
    
    protected $fillable = [
        'flow_type_id', 'user_id', 'media_id', 'logs', 'enabled',
    ];
    const STATES = ['待审批', '同意', '拒绝'];
    
    /** @return BelongsTo */
    function flowType() { return $this->belongsTo('App\Models\FlowType'); }
    
    /** @return BelongsTo */
    function user() { return $this->belongsTo('App\Models\User'); }
    
    /** @return BelongsTo */
    function media() { return $this->belongsTo('App\Models\Media'); }
    
    /**
     * 审批列表
     *
     * @return array
     */
    function index() {
        
        $columns = [
            ['db' => 'Flow.id', 'dt' => 0],
            ['db' => 'FlowType.name as ft', 'dt' => 1],
            [
                'db'        => 'Flow.logs', 'dt' => 2,
                'formatter' => function ($d) {
                    return $this->step($d);
                },
            ],
            ['db' => 'User.realname', 'dt' => 3],
            ['db' => 'Flow.created_at', 'dt' => 4, 'dr' => true],
            ['db' => 'Flow.updated_at', 'dt' => 5, 'dr' => true],
            [
                'db'        => 'Flow.enabled', 'dt' => 6,
                'formatter' => function ($d, $row) {
                    $owner = $row['user_id'] == Auth::id();
                    $id = $row['id'];
                    [$uriEdit, $uriDel] = array_map(
                        function ($name, $title, $class) use ($id) {
                            return $this->anchor($name . $id, $title, $class);
                        }, ['edit_', ''], [$owner ? '编辑' : '审批', '删除'],
                        ['fa-pencil', 'fa-remove text-red']
                    );
                    $uris = (new Action)->uris();
                    [$edit, $del] = array_map(
                        function ($action, $html) use ($uris) {
                            return Auth::user()->can('act', $uris[$action]) ? $html : '';
                        }, ['create', 'destroy'], [$uriEdit, $uriDel]
                    );
                    
                    return $this->state($d) . $edit . ($owner ? $del : '');
                },
            ],
            ['db' => 'Flow.user_id', 'dt' => 7],
            ['db' => 'Flow.flow_type_id', 'dt' => 8],
        ];
        $joins = [
            [
                'table'      => 'flow_types',
                'alias'      => 'FlowType',
                'type'       => 'INNER',
                'conditions' => [
                    'FlowType.id = Flow.flow_type_id',
                ],
            ],
            [
                'table'      => 'users',
                'alias'      => 'User',
                'type'       => 'INNER',
                'conditions' => [
                    'User.id = Flow.user_id',
                ],
            ],
        ];
        $condition = 'Flow.id IN (' . $this->flowIds() . ') OR Flow.user_id = ' . Auth::id();
        
        return Datatable::simple(
            $this, $columns, $joins, $condition
        );
        
    }
    
    /**
     * 保存审批
     *
     * @param array $data
     * @return bool
     */
    function store(array $data) {
        
        return $this->create($data) ? true : false;
        
    }
    
    /**
     * 更新审批
     *
     * @param array $data
     * @param $id
     * @return bool
     * @throws Throwable
     */
    function modify(array $data, $id) {
        
        return $this->revise(
            $this, $data, $id, null
        );
        
    }
    
    /**
     * 删除审批
     *
     * @param null $id
     * @return bool
     * @throws Throwable
     */
    function remove($id = null) {
        
        return $this->purge($id);
        
    }
    
    /**
     * 返回当前登录用户可审批的流程id
     *
     * @return string
     */
    function flowIds() {
        
        $userId = Auth::id();
        $flowIds = collect([]);
        foreach ($this->all() as $flow) {
            if (!$flow->enabled) continue;
            foreach (json_decode($flow->logs, true) ?? [] as $log) {
                if (in_array($userId, $log['userIds']) && isset($log['status']) && !$log['status']) {
                    $flowIds->push($flow->id);
                }
            }
        }
        
        return $flowIds->isNotEmpty() ? $flowIds->join(',') : '0';
        
    }
    
    /**
     * 返回当前流程审批步骤
     *
     * @param $data
     * @return string|null
     */
    function step($data) {
        
        $logs = json_decode($data, true) ?? [];
        for ($i = 0; $i < sizeof($logs); $i++) {
            $status = $logs[$i]['status'];
            if (isset($status)) {
                $step = $logs[$i]['name'] . '[' . self::STATES[$status] . ']';
                if ($i == sizeof($logs) - 1 || $status == 2) {
                    $step .= '[已结束]';
                }
                break;
            }
        }
        
        return $step ?? null;
        
    }
    
    /** @return array */
    function compose() {
        
        $action = explode('/', Request::path())[1];
        switch ($action) {
            case 'index':
                return [
                    'titles' => [
                        '#', '审批类型', '当前步骤', '发起人',
                        ['title' => '创建于', 'html' => $this->htmlDTRange('创建于')],
                        ['title' => '更新于', 'html' => $this->htmlDTRange('更新于')],
                        [
                            'title' => '状态 . 操作',
                            'html'  => $this->htmlSelect(
                                [null => '全部', 0 => '禁用', 1 => '启用'],
                                'filter_enabled'
                            ),
                        ],
                    ],
                    'batch'  => true,
                    'filter' => true,
                ];
            case 'create':
                return [
                    'flowTypes' => FlowType::whereSchoolId($this->schoolId())->pluck('name', 'id'),
                ];
            default: # edit:
                $states = ['待审批', '同意', '拒绝'];
                $flow = $this->find(Request::route('id'));
                $logs = json_decode($flow->logs, true) ?? [];
                $steps = [];
                $flowType = $flow->flowType->name;
                $timeIcon = Html::tag('i', '', ['class' => 'fa fa-clock-o'])->toHtml();
                $completed = false;
                $status = 1;
                for ($i = 0; $i < sizeof($logs); $i++) {
                    if (!isset($status)) break;
                    if ($i == 0) {
                        $time = $flow->created_at;
                        $name = $flow->user_id == Auth::id() ? '我' : $flow->user->realname;
                        $action = '发起了' . $flowType . '审批请求';
                    } else {
                        $time = $status == 0 ? '' : $logs[$i]['time'];
                        $name = $status == 0 ? '' : User::find($logs[$i]['userId'])->realname;
                        $action = $states[$status] . ($status == 0 ? '' : '该请求');
                    }
                    $steps[] = [
                        'step'   => $i,
                        'status' => $status,
                        'time'   => empty($time) ? '' : $timeIcon . $this->humanDate($time),
                        'header' => Html::link('#', $name)->toHtml() . $action,
                        'detail' => $this->detail($logs[$i]),
                    ];
                    if ($status == 2) {
                        $completed = true;
                        break;
                    }
                    $status = $logs[$i]['status'];
                }
                if (sizeof($steps) == sizeof($logs) && $status == 1) {
                    $completed = true;
                }
                
                return [
                    'steps'     => $steps,
                    'completed' => $completed,
                ];
            
        }
        
    }
    
    /**
     * @param array $log
     * @return mixed|string
     */
    private function detail(array $log) {
        
        if ($log['status'] == 0) return '';
        $detail = $log['remark'] ?? '';
        if ($mediaIds = $log['media_ids'] ?? null) {
            foreach (explode(',', $mediaIds) as $mediaId) {
                $media = Media::find($mediaId);
                $mediaType = $media->mediaType->name;
                $path = $media->path;
                if ($mediaType == 'image') {
                    $detail .= Html::image($path, null, ['style' => 'height: 64px;'])->toHtml();
                } elseif ($mediaType == 'video') {
                    $detail .= Html::tag(
                        'video',
                        Html::tag('source', null, ['src' => $path, 'type' => 'video/mp4']),
                        ['height' => '200', 'controls']
                    )->toHtml();
                } else {
                    $paths = explode('/', $path);
                    $filename = mb_substr($paths[sizeof($paths) - 1], 0, 32);
                    $detail .= Html::link($path, $filename, null, false)->toHtml();
                }
            }
        };
        
        return $detail;
        
    }
    
}
