<?php
namespace App\Models;

use App\Facades\Datatable;
use App\Helpers\ModelTrait;
use App\Http\Requests\CardRequest;
use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\{Builder, Model, Relations\BelongsTo};
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
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
    
    protected $fillable = ['sn', 'user_id', 'status'];
    
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
            ['db' => 'User.realname', 'dt' => 1],
            ['db' => 'Card.sn', 'dt' => 2],
            ['db' => 'Card.created_at', 'dt' => 3, 'dr' => true],
            ['db' => 'Card.updated_at', 'dt' => 4, 'dr' => true],
            [
                'db' => 'Card.status', 'dt' => 5,
                'formatter' => function ($d) {
                    return $d ? ($d == 1 ? '正常' : '挂失') : '待发';
                }
            ]
        ];
        $joins = [
            [
                'table' => 'cards',
                'alias' => 'Card',
                'type'  => 'LEFT',
                'conditions' => [
                    'Card.id' => 'User.card_id'
                ]
            ]
        ];
        $condition = 'User.id IN (' . $this->visibleUserIds() . ')';
        
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
                                'sn' => $data['card_sn'],
                                'user_id' => $user->id,
                                'status' => 1,
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
                                        'status' => 1,
                                    ])
                                );
                                $user->update(['card_id' => $card->id]);
                            }
                        } else {
                            $user->card->delete();
                        }
                    }
                } else {
                    foreach (Request::input('data') as $userId => $sn) {
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
     * @param CardRequest $request
     * @return bool
     * @throws Throwable
     */
    function modify(CardRequest $request) {
        
        try {
            DB::transaction(function () use ($request) {
                if ($request->route('id')) {
                    $this->find($request->route('id'))->update(
                        $request->all()
                    );
                } else {
                    $this->whereIn('user_id', array_values($request->input('ids')))
                        ->update([
                            'status' => $request->input('action') == 'enable' ? 1 : 0
                        ]);
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

}