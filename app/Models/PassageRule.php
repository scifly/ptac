<?php
namespace App\Models;

use App\Facades\Datatable;
use App\Helpers\ModelTrait;
use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\{Builder,
    Collection,
    Model,
    Relations\BelongsTo,
    Relations\BelongsToMany,
    Relations\HasMany};
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use Throwable;

/**
 * 门禁通行规则
 * 
 * Class PassageRule
 *
 * @package App\Models
 * @property int $id
 * @property int $school_id 学校id
 * @property string $name 通行规则名称
 * @property int $ruleid 通行规则id: 2 - 254
 * @property int $start_date 通行规则起始日期
 * @property int $end_date 通行规则结束日期
 * @property string $statuses 适用日：Mon - Sun
 * @property string $tr1 时段1：00:00 - 13:33
 * @property string $tr2 时段2
 * @property string $tr3 时段3
 * @property string $targets 作用范围
 * @property int $related_ruleid 关联的通行规则id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $enabled 通行规则状态
 * @property-read Collection|Card[] $cards
 * @property-read School $school
 * @property-read Collection|Turnstile[] $turnstiles
 * @method static Builder|PassageRule newModelQuery()
 * @method static Builder|PassageRule newQuery()
 * @method static Builder|PassageRule query()
 * @method static Builder|PassageRule whereCreatedAt($value)
 * @method static Builder|PassageRule whereEnabled($value)
 * @method static Builder|PassageRule whereEndDate($value)
 * @method static Builder|PassageRule whereId($value)
 * @method static Builder|PassageRule whereName($value)
 * @method static Builder|PassageRule whereRelatedRuleid($value)
 * @method static Builder|PassageRule whereRuleid($value)
 * @method static Builder|PassageRule whereSchoolId($value)
 * @method static Builder|PassageRule whereStartDate($value)
 * @method static Builder|PassageRule whereStatuses($value)
 * @method static Builder|PassageRule whereTr1($value)
 * @method static Builder|PassageRule whereTr2($value)
 * @method static Builder|PassageRule whereTr3($value)
 * @method static Builder|PassageRule whereTargets($value)
 * @method static Builder|PassageRule whereUpdatedAt($value)
 * @mixin Eloquent
 * @property-read int|null $cards_count
 * @property-read int|null $turnstiles_count
 */
class PassageRule extends Model {
    
    use ModelTrait;
    
    protected $table = 'passage_rules';
    
    protected $fillable = [
        'school_id', 'name', 'ruleid',
        'start_date', 'end_date', 'statuses',
        'tr1', 'tr2', 'tr3', 'targets',
        'related_ruleid', 'enabled',
    ];
    
    /** Properties -------------------------------------------------------------------------------------------------- */
    /** @return BelongsTo */
    function school() { return $this->belongsTo('App\Models\School'); }
    
    /** @return HasMany */
    function cards() { return $this->hasMany('App\Models\Card'); }
    
    /** @return BelongsToMany */
    function turnstiles() {
        
        return $this->belongsToMany(
            'App\Models\Turnstile',
            'rule_turnstile',
            'passage_rule_id',
            'turnstile_id'
        );
        
    }
    
    /** crud -------------------------------------------------------------------------------------------------------- */
    /**
     * 通行规则列表
     *
     * @return array
     */
    function index() {
        
        $columns = [
            ['db' => 'PassageRule.id', 'dt' => 0],
            ['db' => 'PassageRule.name', 'dt' => 1],
            ['db' => 'PassageRule.ruleid', 'dt' => 2],
            ['db' => 'PassageRule.start_date', 'dt' => 3],
            ['db' => 'PassageRule.end_date', 'dt' => 4],
            ['db' => 'PassageRule.statuses', 'dt' => 5],
            ['db' => 'PassageRule.tr1', 'dt' => 6],
            ['db' => 'PassageRule.tr2', 'dt' => 7],
            ['db' => 'PassageRule.tr3', 'dt' => 8],
            ['db' => 'PassageRule.related_ruleid', 'dt' => 9],
            [
                'db'        => 'PassageRule.enabled', 'dt' => 10,
                'formatter' => function ($d, $row) {
                    return Datatable::status($d, $row, false);
                },
            ],
        ];
        $condition = 'PassageRule.school_id = ' . $this->schoolId();
        
        return Datatable::simple(
            $this, $columns, null, $condition
        );
        
    }
    
    /**
     * 保存通行规则
     *
     * @param array $data
     * @return bool
     * @throws Throwable
     */
    function store(array $data) {
        
        try {
            DB::transaction(function () use ($data) {
                $pr = $this->create($data);
                (new RuleTurnstile)->store(
                    $pr->id, $doorIds = $data['door_ids'] ?? []
                );
                // $this->issue($this->deviceids($doorIds));
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
    /**
     * 更新通行规则
     *
     * @param array $data
     * @param $id
     * @return bool
     * @throws Throwable
     */
    function modify(array $data, $id) {
        
        return $this->revise(
            $this, $data, $id,
            function (PassageRule $pr) use ($data) {
                (new RuleTurnstile)->store(
                    $pr->id, $doorIds = $data['door_ids'] ?? []
                );
            }
        );
        
    }
    
    /**
     * 删除通行规则
     *
     * @param $id
     * @return bool|null
     * @throws Throwable
     */
    function remove($id) {
        
        return $this->purge($id, [
            'purge.passage_rule_id' => ['RuleTurnstile']
        ]);
        
    }
    
    /**
     * 下发通行规则
     *
     * @param array $deviceids
     * @return bool
     * @throws Exception
     */
    function issue($deviceids = []) {
        
        try {
            $devices = empty($deviceids)
                ? Turnstile::whereSchoolId($this->schoolId())->get()
                : Turnstile::whereIn('deviceid', array_values($deviceids))->get();
            !empty($deviceids) ?: $deviceids = $devices->pluck('deviceid')->toArray();
            $rules = [];
            foreach ($devices as $device) {
                foreach ($device->passageRules as $pr) {
                    $index = $pr->ruleid;
                    list($s_date, $e_date) = array_map(
                        function ($field) use ($pr) {
                            return date('Ymd', strtotime($pr->{$field}));
                        }, ['start_date', 'end_date']
                    );
                    $week = join(array_map(
                        function ($chr) { return '0' . $chr; },
                        str_split($pr->statuses)
                    ));
                    $tzones = array_map(
                        function ($field) use ($pr) {
                            return join(array_map(
                                function ($time) {
                                    return date('Hi', strtotime($time));
                                }, explode(' - ', $pr->{$field})
                            ));
                        }, ['tr1', 'tr2', 'tr3']
                    );
                    $rules[$device->deviceid][] = array_combine(
                        ['index', 's_date', 'e_date', 'week', 'tzones', 'time_frame'],
                        [$index, $s_date, $e_date, $week, $tzones, $pr->related_ruleid]
                    );
                }
            }
            array_map(
                function ($api, $data) {
                    empty($data) ?: (new Turnstile)->invoke($api, ['data' => $data]);
                }, ['tfclr', 'tfset'], [$deviceids, $rules]
            );
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }

    /** Helper functions */
    /**
     * 返回create/edit view所需数据
     *
     * @return array
     */
    function compose() {
    
        $action = explode('/', Request::path())[1];
        if ($action == 'index') {
            $data = [
                'buttons'        => [
                    'issue' => [
                        'id' => 'issue',
                        'label' => '下发规则',
                        'icon' => 'fa fa-minus-circle'
                    ],
                ],
                'titles' => [
                    '#', '名称', '规则id',
                    [
                        'title' => '起始日期',
                        'html'  => $this->htmlDTRange('起始日期', false),
                    ],
                    [
                        'title' => '结束日期',
                        'html'  => $this->htmlDTRange('结束日期', false),
                    ],
                    '适用范围', '时段1', '时段2', '时段3', '关联规则id',
                    [
                        'title' => '状态 . 操作',
                        'html'  => $this->htmlSelect(
                            collect([null => '全部', '禁用', '启用']), 'filter_enabled'
                        ),
                    ],
                ],
            ];
        } else {
            $doors = (new Turnstile)->doors();
            $pr = PassageRule::find(Request::route('id'));
            $trs = !$pr ? null : array_map(
                function ($field) use ($pr) {
                    return explode(' - ', $pr->{$field});
                }, ['tr1', 'tr2', 'tr3']
            );
            $rts = RuleTurnstile::wherePassageRuleId($pr ? $pr->id : null)->get();
            $selectedDoors = collect([]);
            foreach ($rts as $rt) {
                $t = $rt->turnstile;
                $door = join('.', [$t->sn, $rt->door, $t->location]);
                $selectedDoors[array_search($door, $doors)] = $door;
            }
            $ruleids = $this->whereSchoolId($this->schoolId())->pluck('name', 'ruleid');
            if ($pr && $pr->ruleid) unset($ruleids[$pr->ruleid]);
    
            $data = array_combine(
                ['pr', 'weekdays', 'trs', 'doors', 'selectedDoors', 'ruleids'],
                [
                    $pr,
                    str_split($pr ? $pr->statuses : '0000000'),
                    $trs ?? array_fill(
                        0, 3, array_fill(0, 2, '00:00')
                    ),
                    $doors, $selectedDoors,
                    $ruleids->merge([0 => '(无关联规则)'])->sort(),
                ]
            );
        }
    
        return $data;
    
    }
    
}
