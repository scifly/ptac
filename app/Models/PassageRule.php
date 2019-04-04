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
use Illuminate\Support\Facades\Log;
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
    
    /**
     * 返回通行规则所属的学校对象
     *
     * @return BelongsTo
     */
    function school() { return $this->belongsTo('App\Models\School'); }
    
    /**
     * 获取通行规则对应的一卡通对象
     *
     * @return HasMany
     */
    function cards() { return $this->hasMany('App\Models\Card'); }
    
    /**
     * 返回指定通行规则对应的所有门禁对象
     *
     * @return BelongsToMany
     */
    function turnstiles() {
        
        return $this->belongsToMany(
            'App\Models\Turnstile',
            'rules_turnstiles',
            'passage_rule_id',
            'turnstile_id'
        );
        
    }
    
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
                $this->issue(array_values($this->deviceids($doorIds)));
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
        
        try {
            DB::transaction(function () use ($data, $id) {
                $this->find($id)->update($data);
                (new RuleTurnstile)->store(
                    $id, $doorIds = $data['door_ids'] ?? []
                );
                $this->issue($this->deviceids($doorIds));
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
    /**
     * 删除通行规则
     *
     * @param $id
     * @return bool|null
     * @throws Throwable
     */
    function remove($id) {
        
        try {
            DB::transaction(function () use ($id) {
                $this->find($id)->delete();
                (new RuleTurnstile)->wherePassageRuleId($id)->delete();
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
    /**
     * 返回create/edit view所需数据
     *
     * @return array
     */
    function compose() {
        
        $doors = (new Turnstile)->doors();
        $ruleids = $this->whereSchoolId($this->schoolId())->pluck('name', 'ruleid')->toArray();
        if (Request::route('id')) {
            $pr = PassageRule::find(Request::route('id'));
            $weekdays = str_split($pr->statuses);
            $trs = array_map(
                function ($field) use ($pr) {
                    return explode(' - ', $pr->{$field});
                }, ['tr1', 'tr2', 'tr3']
            );
            $rts = (new RuleTurnstile)->wherePassageRuleId($pr->id)->get();
            $selectedDoors = [];
            foreach ($rts as $rt) {
                $t = $rt->turnstile;
                $door = implode('.', [$t->sn, $rt->door, $t->location]);
                $selectedDoors[array_search($door, $doors)] = $door;
            }
            if ($pr->ruleid) {
                unset($ruleids[$pr->ruleid]);
            }
        }
        
        return [
            $pr ?? null,
            $weekdays ?? str_split('0000000'),
            $trs ?? array_fill(
                0, 3, array_fill(0, 2, '00:00')
            ),
            $doors, $selectedDoors ?? null,
            $ruleids,
        ];
        
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
                : Turnstile::whereIn('deviceid', $deviceids)->get();
            $rules = [];
            foreach ($devices as $device) {
                foreach ($device->passageRules as $pr) {
                    $index = $pr->ruleid;
                    list($s_date, $e_date) = array_map(
                        function ($field) use ($pr) {
                            return date('Ymd', strtotime($pr->{$field}));
                        }, ['start_date', 'end_date']
                    );
                    $week = implode('',
                        array_map(
                            function ($chr) { return '0' . $chr; },
                            str_split($pr->statuses)
                        )
                    );
                    $tzones = array_map(
                        function ($field) use ($pr) {
                            return implode('', array_map(
                                    function ($time) {
                                        return date('Hi', strtotime($time));
                                    }, explode(' - ', $pr->{$field})
                                )
                            );
                        }, ['tr1', 'tr2', 'tr3']
                    );
                    $rules[$device->deviceid][] = array_combine(
                        ['index', 's_date', 'e_date', 'week', 'tzones', 'time_frame'],
                        [$index, $s_date, $e_date, $week, $tzones, $pr->related_ruleid]
                    );
                }
            }
            Log::info('rules', $rules);
            array_map(
                function ($api, $data) { (new Turnstile)->invoke($api, ['data' => $data]); },
                ['clrtimeframes', 'settimeframes'],
                [$devices->pluck('deviceid')->toArray(), $rules]
            );
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
    
    }
    
    /**
     * 返回门禁设备deviceid数组
     *
     * @param $doorIds
     * @return array
     */
    private function deviceids($doorIds) {
    
        $doors = (new Turnstile)->doors();
        foreach ($doorIds as $id) {
            list($sn) = explode('.', $doors[$id]);
            $deviceids[] = Turnstile::whereSn($sn)->first()->deviceid;
        }
        
        return array_unique($deviceids ?? []);
        
    }
    
}
