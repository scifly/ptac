<?php
namespace App\Models;

use App\Facades\Datatable;
use App\Helpers\HttpStatusCode;
use App\Helpers\ModelTrait;
use App\Helpers\Snippet;
use App\Http\Requests\CardRequest;
use Eloquent;
use Exception;
use Form;
use Illuminate\Database\Eloquent\{Builder, Model, Relations\BelongsTo};
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\{DB, Request};
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
    
    protected $fillable = [
        'sn', 'user_id', 'status',
    ];
    
    /**
     * 获取一卡通对应的用户对象
     *
     * @return BelongsTo
     */
    function user() { return $this->belongsTo('App\Models\User'); }
    
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
            ['db' => 'User.realname', 'dt' => 2],
            ['db' => 'Groups.name', 'dt' => 3],
            [
                'db'        => 'User.id as userId', 'dt' => 4,
                'formatter' => function ($d) {
                    return User::find($d)->mobiles->where('isdefault', 1)->first()->mobile;
                },
            ],
            [
                'db'        => 'Card.created_at', 'dt' => 5, 'dr' => true,
                'formatter' => function ($d) { return $d ?? ' - '; },
            ],
            [
                'db'        => 'Card.updated_at', 'dt' => 6, 'dr' => true,
                'formatter' => function ($d) { return $d ?? ' - '; },
            ],
            [
                'db'        => 'Card.status', 'dt' => 7,
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
            ['db' => 'User.card_id', 'dt' => 8],
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
        $sGId = Group::whereName('学生')->first()->id;
        $condition = 'User.id IN (' . $this->visibleUserIds() . ') AND User.group_id <> ' . $sGId;
        
        return Datatable::simple(new User, $columns, $joins, $condition);
        
    }
    
    /**
     * 保存一卡通
     *
     * @param User|null $user
     * @return bool
     * @throws Throwable
     */
    function store(User $user = null) {
        
        try {
            DB::transaction(function () use ($user) {
                $data = Request::all();
                if ($user) {
                    if (Request::method() == 'POST') {
                        if (!empty($data['card']['sn'])) {
                            $card = Card::create([
                                'sn'      => $data['card_sn'],
                                'user_id' => $user->id,
                                'status'  => 1,
                            ]);
                            $user->update(['card_id' => $card->id]);
                        }
                    } else {
                        if (!empty($data['card']['sn'])) {
                            if ($user->card) {
                                $user->card->update($data['card']);
                            } else {
                                $card = Card::create(
                                    array_merge($data['card'], [
                                        'user_id' => $user->id,
                                        'status'  => 1,
                                    ])
                                );
                                $user->update(['card_id' => $card->id]);
                            }
                        } else {
                            !$user->card ?: $user->card->delete();
                        }
                    }
                } else {
                    foreach (Request::input('sns') as $userId => $sn) {
                        $records[] = ['sn' => $sn, 'user_id' => $userId, 'status' => 1];
                        $userIds[] = $userId;
                    }
                    $this->insert($records ?? []);
                    $cards = $this->whereIn('user_id', $userIds ?? [])->get();
                    /** @var Card $card */
                    foreach ($cards as $card) {
                        $card->user->update(['card_id' => $card->id]);
                    }
                }
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
    /**
     * 更新一卡通(挂失、解挂、换卡)
     *
     * @return bool
     * @throws Throwable
     */
    function modify() {
        
        $this->validate($cards = Request::input('sns'));
        try {
            DB::transaction(function () use ($cards) {
                foreach ($cards as $userId => $card) {
                    $user = User::find($userId);
                    $sn = $card['sn'];
                    $status = $card['status'];
                    if ($sn) {
                        $data = ['status' => $status];
                        if ($user->card->sn != $sn) {
                            abort_if(
                                Card::whereSn($sn)->first() ? true : false,
                                HttpStatusCode::NOT_ACCEPTABLE,
                                __('卡号已被使用')
                            );
                            $data = array_merge($data, ['sn' => $sn]);
                        }
                        $user->card->update($data);
                    } else {
                        $user->card->delete();
                        $user->update(['card_id' => null]);
                    }
                }
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
    /**
     * 注销/删除一卡通
     *
     * @param CardRequest $request
     * @return bool
     * @throws Throwable
     */
    function remove(CardRequest $request) {
        
        try {
            DB::transaction(function () use ($request) {
                $userIds = $request->route('id')
                    ? [$request->route('id')]
                    : array_values($request->input('ids'));
                $this->whereIn('user_id', $userIds)->delete();
                User::whereIn('id', $userIds)
                    ->update(['card_id' => null]);
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
    /**
     * 返回卡号输入框html
     *
     * @return mixed
     */
    function input() {
        
        return Form::text('sn', '%s', [
            'class'     => 'form-control text-blue input-sm',
            'maxlength' => 10,
            'data-uid'  => '%s',
            'data-seq'  => '%s',
        ])->toHtml();
        
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
     * 通讯录批量发卡
     *
     * @return JsonResponse
     * @throws Throwable
     */
    function issue() {
        
        $this->validate($sns = Request::input('sns'));
        try {
            DB::transaction(function () use ($sns) {
                foreach ($sns as $userId => $sn) {
                    if (!empty($sn)) {
                        $card = Card::whereUserId($userId)->first();
                        $_sn = Card::whereSn($sn)->first();
                        if (!$card) {
                            abort_if(
                                $_sn ? true : false,
                                HttpStatusCode::NOT_ACCEPTABLE,
                                __('卡号已被使用')
                            );
                            $card = Card::create([
                                'user_id' => $userId,
                                'sn'      => $sn,
                                'status'  => 1,
                            ]);
                        } else {
                            if ($card->sn != $sn) {
                                abort_if(
                                    Card::whereSn($sn)->first() ? true : false,
                                    HttpStatusCode::NOT_ACCEPTABLE,
                                    __('卡号已被使用')
                                );
                                $card->update(['sn' => $sn]);
                            }
                        }
                        $card->user->update(['card_id' => $card->id]);
                    } else {
                        $user = User::find($userId);
                        if ($user->card) {
                            $user->card->delete();
                            $user->update(['card_id' => 0]);
                        }
                    }
                }
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return response()->json([
            'title'   => '批量发卡',
            'message' => __('messages.ok'),
        ]);
        
    }
    
    /**
     * 批量授权
     *
     * @param string $type
     * @return JsonResponse|string
     * @throws Throwable
     */
    function permit($type) {
        
        if (Request::has('sectionId')) {
            if ($type == 'Educator') {
                $users = Department::find(Request::input('section_id'))->users->filter(
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
                $userIds = $input['userIds'];
                $turnstileIds = $input['turnstileIds'];
                $ruleids = $input['ruleids'];
                list($start, $end) = explode(' - ', $input['dateRange']);
                (new CardTurnstile)->store(
                    Card::whereIn('user_id', $userIds)->get()->pluck('id')->toArray(),
                    $turnstileIds, $start, $end, $ruleids
                );
                $data = [];
                foreach ($turnstileIds as $turnstileId) {
                    $deviceid = Turnstile::find($turnstileId)->deviceid;
                    foreach ($userIds as $userId) {
                        $user = User::find($userId);
                        if (!$user->card) continue;
                        $data[$deviceid] = [
                            'card' => $user->card->sn,
                            's_date' => $start,
                            'e_date' => $end,
                            'time_frames' => $ruleids[$turnstileId]
                        ];
                    }
                }
                (new Turnstile)->invoke('adddevperms', $data);
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
                if ($i <= $t->doors) {
                    $prIds = RuleTurnstile::where(['turnstile_id' => $t->id, 'door' => $i])
                        ->get()->pluck('passage_rule_id')->toArray();
                    $_prs = PassageRule::whereIn('id', $prIds)
                            ->pluck('name', 'ruleid')->toArray();
                }
                $doors .= sprintf(
                    $td, Form::select(
                        'ruleids[' . $t->id . '][]',
                        array_merge($prs, $_prs ?? []), null,
                        [
                            'class'    => 'form-control select2 input-sm',
                            'style'    => 'width: 100%;',
                            'disabled' => sizeof(array_merge($prs, $_prs ?? [])) <= 1
                        ]
                    )->toHtml());
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
     * @param array $cards
     */
    private function validate(array $cards) {
        
        $sns = array_values($cards);
        if (is_array($sns[0])) {
            $sns = Arr::pluck($sns, 'sn');
        }
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
    
}