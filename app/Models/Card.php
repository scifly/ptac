<?php
namespace App\Models;

use App\Facades\Datatable;
use App\Helpers\{HttpStatusCode, ModelTrait, Snippet};
use Eloquent;
use Exception;
use Form;
use Illuminate\Database\Eloquent\{Builder, Collection, Model, Relations\BelongsTo, Relations\BelongsToMany};
use Illuminate\Http\JsonResponse;
use Illuminate\Support\{Arr, Carbon, Facades\DB, Facades\Log, Facades\Request};
use Throwable;

/**
 * 一卡通
 *
 * Class Card
 *
 * @package App\Models
 * @property int $id
 * @property string $sn 卡号
 * @property int $user_id 所属用户id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $status 状态
 * @property-read User $user
 * @property-read Collection|Turnstile[] $turnstiles
 * @method static Builder|Card newModelQuery()
 * @method static Builder|Card newQuery()
 * @method static Builder|Card query()
 * @method static Builder|Card whereCreatedAt($value)
 * @method static Builder|Card whereId($value)
 * @method static Builder|Card whereStatus($value)
 * @method static Builder|Card whereUpdatedAt($value)
 * @method static Builder|Card whereUserId($value)
 * @method static Builder|Card whereSn($value)
 * @mixin Eloquent
 */
class Card extends Model {
    
    use ModelTrait;
    
    protected $fillable = ['user_id', 'sn', 'status'];
    
    /**
     * 获取一卡通对应的用户对象
     *
     * @return BelongsTo
     */
    function user() { return $this->belongsTo('App\Models\User'); }
    
    /**
     * 获取一卡通绑定的所有门禁对象
     *
     * @return BelongsToMany
     */
    function turnstiles() {
        
        return $this->belongsToMany('App\Models\Turnstile', 'cards_turnstiles');
        
    }
    
    /**
     * 一卡通列表
     *
     * @return array
     */
    function index() {
        
        $columns = [
            ['db' => 'User.id', 'dt' => 0],
            [
                'db'        => 'Card.sn', 'dt' => 1,
                'formatter' => function ($d) {
                    return $d ?? sprintf(Snippet::BADGE, 'text-gray', '[尚未发卡]');
                },
            ],
            [
                'db'        => 'User.card_id', 'dt' => 2,
                'formatter' => function ($d) {
                    if (!$card = $this->find($d)) return '';
                    $turnstileIds = $card->turnstiles->pluck('id')->toArray();
                    $prIds = RuleTurnstile::whereIn('turnstile_id', $turnstileIds)
                        ->pluck('passage_rule_id')->toArray();
                    
                    return implode('<br />',
                        PassageRule::whereIn('id', array_unique($prIds))
                            ->pluck('name')->toArray()
                    );
                },
            ],
            ['db' => 'User.realname', 'dt' => 3],
            ['db' => 'Groups.name', 'dt' => 4],
            [
                'db'        => 'User.id as userId', 'dt' => 5,
                'formatter' => function ($d) {
                    $default = User::find($d)->mobiles->where('isdefault', 1)->first();
                    
                    return $default ? $default->mobile : 'n/a';
                },
            ],
            [
                'db'        => 'Card.created_at', 'dt' => 6, 'dr' => true,
                'formatter' => function ($d) { return $d ?? ' - '; },
            ],
            [
                'db'        => 'Card.updated_at', 'dt' => 7, 'dr' => true,
                'formatter' => function ($d) { return $d ?? ' - '; },
            ],
            [
                'db'        => 'Card.status', 'dt' => 8,
                'formatter' => function ($d, $row) {
                    
                    $colors = [
                        ['text-gray', '待发'],
                        ['text-green', '正常'],
                        ['text-red', '挂失'],
                    ];
                    $status = sprintf(
                        Snippet::BADGE,
                        $colors[$d ?? 0][0], $colors[$d ?? 0][1]
                    );
                    
                    return $row['card_id'] ? Datatable::status($status, $row, false) : $status;
                },
            ],
        ];
        $joins = [
            [
                'table'      => 'cards',
                'alias'      => 'Card',
                'type'       => 'LEFT',
                'conditions' => [
                    'Card.id = User.card_id',
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
        ];
        // $sGId = Group::whereName('学生')->first()->id;
        $condition = 'User.id IN (' . $this->visibleUserIds() . ')'; // AND User.group_id <> ' . $sGId;
        
        return Datatable::simple(new User, $columns, $joins, $condition);
        
    }
    
    /**
     * 保存一卡通 - (批量)发卡、删卡、换卡
     *
     * @param User|null $user
     * @param bool $issue
     * @return string|bool
     * @throws Throwable
     */
    function store(User $user = null, $issue = false) {
        
        try {
            DB::transaction(function () use ($user) {
                !$user ?: Request::merge(['sns' => [$user->id => Request::input('card')['sn']]]);
                $sns = Request::input('sns');
                $this->validate(array_values($sns));
                $inserts = $replaces = $purges = $userIds = [];
                foreach ($sns as $userId => $sn) {
                    if (!empty($sn)) {
                        if (!$card = Card::whereUserId($userId)->first()) {
                            $inserts[] = array_combine($this->fillable, [$userId, $sn, 1]);
                            $userIds[] = $userId;
                        } else {
                            if ($card->sn != $sn) {
                                $this->exists($sn);
                                $replaces[$userId] = $sn;
                            }
                        }
                    } else {
                        !User::find($userId)->card ?: $purges[] = $userId;
                    }
                }
                # 发卡
                $this->insert($inserts ?? []);
                $cards = $this->whereIn('user_id', $userIds)->get();
                foreach ($cards as $card) { $card->user->update(['card_id' => $card->id]); }
                # 换卡
                Request::merge(['sns' => $replaces]); $this->modify();
                # 删卡
                Request::merge(['ids' => $purges]); $this->remove();
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return !$issue ? true : response()->json([
            'title' => '批量发卡',
            'message' => __('messages.ok')
        ]);
        
    }
    
    /**
     * 更新一卡通(挂失、解挂、换卡)
     *
     * @return bool
     * @throws Throwable
     */
    function modify() {
    
        $cards = Request::input('sns');
        $this->validate(Arr::pluck(array_values($cards), 'sn'));
        try {
            DB::transaction(function () use ($cards) {
                $inserts = [];
                foreach ($cards as $userId => $card) {
                    $user = User::find($userId);
                    Request::merge(['ids' => [$userId]]);
                    if ($sn = is_array($card) ? $card['sn'] : $card) {
                        $data = ['status' => ($status = $card['status'] ?? 1)];
                        if ($user->card->sn != $sn) {   # 换卡
                            $this->exists($sn);
                            $this->remove(true);
                            $data = array_merge($data, ['sn' => $sn]);
                        } else {
                            if ($status == 1) {
                                $tList = $user->card->turnstiles->pluck('deviceid', 'id')->toArray();
                                foreach ($tList as $tId => $deviceid) {
                                    $inserts[$deviceid][] = $this->perm($user->card_id, $tId);
                                }
                            } else {
                                $this->remove(true); # 挂失
                            }
                        }
                        $user->card->update($data);
                    } else {
                        $this->remove();    # 删卡
                    }
                }
                empty($inserts) ?: (new Turnstile)->invoke('addperms', ['data' => $inserts]);
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
    /**
     * 注销/删除一卡通
     *
     * @param bool $soft - true: 仅删除已下发的一卡通设备权限
     * @return bool
     * @throws Throwable
     */
    function remove($soft = false) {
        
        try {
            DB::transaction(function () use ($soft) {
                $userIds = (Request::route('id') && stripos('delete', Request::path()) !== false)
                    ? [Request::route('id')]
                    : array_values(Request::input('ids'));
                $perms = [];
                foreach ($userIds as $userId) {
                    if (!$card = User::find($userId)->card) continue;
                    $tList = $card->turnstiles->pluck('deviceid', 'id')->toArray();
                    foreach ($tList as $tId => $deviceid) {
                        if (!$perm = $this->perm($card->id, $tId)) continue;
                        $perms[$deviceid][] = $perm;
                    }
                }
                # 删除已下发的设备权限
                empty($perms) ?: (new Turnstile)->invoke('delperms', ['data' => $perms]);
                if (!$soft) {
                    $cards = $this->whereIn('user_id', $userIds);
                    CardTurnstile::whereIn('card_id', $cards->pluck('id')->toArray())->delete();
                    $cards->delete();
                    User::whereIn('id', $userIds)->update(['card_id' => 0]);
                }
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
    /**
     * 返回卡号输入框html
     *
     * @param bool $disabled
     * @return mixed
     */
    function input($disabled = false) {
        
        $params = [
            'class'     => 'form-control text-blue input-sm',
            'maxlength' => 10,
            'data-uid'  => '%s',
            'data-seq'  => '%s',
        ];
        if ($disabled) {
            $params = array_merge($params, ['disabled' => true]);
        }
        
        return Form::text('sn', '%s', $params)->toHtml();
        
    }
    
    /**
     * 返回授权选择输入html
     *
     * @param $name
     * @param $class
     * @param bool $checked
     * @return string
     */
    function checkbox($name, $class, $checked = true) {
        
        return Form::checkbox(
            $name, '%s', $checked,
            ['class' => 'minimal ' . $class]
        )->toHtml();
        
    }
    
    /**
     * 返回一卡通状态下拉列表html
     *
     * @param $selected
     * @return string
     */
    function status($selected) {
        
        $items = [1 => '正常', 2 => '挂失'];
        
        return Form::select('status', $items, $selected, [
            'class'    => 'form-control select2 input-sm',
            'style'    => 'width: 100%;',
            'disabled' => sizeof($items) <= 1,
        ])->toHtml();
        
    }
    
    /**
     * 批量授权
     *
     * @param string $type
     * @return JsonResponse|string
     * @throws Throwable
     */
    function grant($type) {
        
        if (Request::has('sectionId')) {
            if ($type == 'Educator') {
                $users = Department::find(Request::input('sectionId'))->users->filter(
                    function (User $user) { return !in_array($user->group->name, ['监护人', '学生']); }
                );
            } else {
                $users = Department::find(Squad::find(Request::input('sectionId'))->department_id)
                    ->users->filter(
                        function (User $user) use ($type) {
                            return $user->group->name == ($type == 'Custodian' ? '监护人' : '学生');
                        }
                    );
            }
            $authHtml = $this->checkbox('user_ids[]', 'contact');
            $row = '<tr>%s</tr>';
            $td = '<td class="text-center" style="vertical-align: middle;">%s</td>';
            $list = '';
            foreach ($users as $user) {
                if (!$user->card) continue;
                $list .= sprintf(
                    $row, implode('', array_map(
                        function ($value) use ($td) { return sprintf($td, $value); },
                        [sprintf($authHtml, $user->id), $user->realname, $user->card->sn]
                    ))
                );
            }
            
            return $list;
        }
        try {
            DB::transaction(function () {
                $input = Request::all();
                $userIds = $input['user_ids'];
                $tIds = $input['turnstile_ids'];
                $ruleids = $input['ruleids'];
                list($start, $end) = isset($input['daterange'])
                    ? explode(' ~ ', $input['daterange'])
                    : array_fill(0, 2, null);
                (new CardTurnstile)->store(
                    Card::whereIn('user_id', $userIds)->pluck('id')->toArray(),
                    $tIds, $start, $end, $ruleids
                );
                $data = [];
                list($sDate, $eDate) = array_map(
                    function ($date) {
                        return $date ? date('Ymd', strtotime($date)) : '00000000';
                    }, [$start, $end]
                );
                foreach ($tIds as $tId) {
                    $deviceid = Turnstile::find($tId)->deviceid;
                    foreach ($userIds as $userId) {
                        $user = User::find($userId);
                        if (!$user->card) continue;
                        $data[$deviceid][] = [
                            'card'        => $user->card->sn,
                            's_date'      => $sDate,
                            'e_date'      => $eDate,
                            'time_frames' => array_pad($ruleids[$tId], 4, 0),
                        ];
                    }
                }
                empty($data) ?: (new Turnstile)->invoke('addperms', ['data' => $data]);
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return response()->json([
            'title'   => '批量授权',
            'message' => __('messages.ok'),
        ]);
        
    }
    
    /**
     * 返回一卡通批量授权页面所需数据
     *
     * @param $type
     * @return array
     */
    function compose($type) {
        
        $turnstiles = Turnstile::whereSchoolId($this->schoolId())->get();
        $tList = [];
        $td = '<td class="text-center" style="vertical-align: middle;">%s</td>';
        foreach ($turnstiles as $t) {
            $id = sprintf(
                $td, sprintf($this->checkbox('turnstile_ids[]', 'gate', false), $t->id)
            );
            $name = sprintf($td, $t->location);
            $doors = '';
            $prs = [0 => '(禁止通行)', 1 => '(无限制)'];
            for ($i = 1; $i <= 4; $i++) {
                $_prs = [];
                if ($i <= $t->doors) {
                    $prIds = RuleTurnstile::where(['turnstile_id' => $t->id, 'door' => $i])
                        ->get()->pluck('passage_rule_id')->toArray();
                    $_prs = PassageRule::orderBy('ruleid')->whereIn('id', $prIds)
                        ->pluck('name', 'ruleid')->toArray();
                }
                $rules = !empty($_prs) ? ($prs + ($_prs ?? [])) : [0 => '(禁止通行)'];
                $doors .= sprintf($td, Form::select('ruleids[' . $t->id . '][]', $rules, null, [
                    'class'    => 'form-control select2 input-sm',
                    'style'    => 'width: 100%;',
                    'disabled' => sizeof($rules) <= 1,
                ])->toHtml());
            }
            $tList[] = '<tr>' . $id . $name . $doors . '</tr>';
        }
        $builder = $type == 'Educator'
            ? Department::whereIn('id', $this->departmentIds())
            : Squad::whereIn('id', $this->classIds());
        
        return [
            'formId'     => 'form' . $type,
            'sections'   => [0 => '(请选择一个部门)'] + $builder->get()->pluck('name', 'id')->toArray(),
            'turnstiles' => implode('', $tList),
        ];
        
    }
    
    /**
     * 检查卡号是否有重复
     *
     * @param array $sns
     */
    private function validate(array $sns) {
        
        $ns = array_count_values(array_map('strval', $sns));
        foreach ($ns as $n => $count) {
            if (!empty($n) && $count > 1) $ds[] = $n;
        }
        abort_if(
            !empty($ds ?? []),
            HttpStatusCode::NOT_ACCEPTABLE,
            implode('', [
                (!empty($sns) ? ('卡号: ' . implode(',', $ds ?? [])) : ''),
                '有重复，请检查后重试',
            ])
        );
        
    }
    
    /**
     * 返回门禁设备权限
     *
     * @param integer $id - 一卡通id
     * @param integer $tId - 门禁id
     * @return array|null
     */
    private function perm($id, $tId) {
    
        $ct = CardTurnstile::where(['card_id' => $id, 'turnstile_id' => $tId])->first();
        return !$ct ? null : [
            'card'        => $this->find($id)->sn,
            's_date'      => date('Ymd', strtotime($ct->start_date)),
            'e_date'      => date('Ymd', strtotime($ct->end_date)),
            'time_frames' => array_pad(
                explode(',', $ct->ruleids), 4, "0"
            ),
        ];
        
    }
    
    /**
     * 判断卡号是否已存在
     *
     * @param $sn
     */
    private function exists($sn) {
    
        abort_if(
            $this->whereSn($sn)->first() ? true : false,
            HttpStatusCode::NOT_ACCEPTABLE,
            __( '卡号：' . $sn . ' 已被使用')
        );
        
    }
    
}