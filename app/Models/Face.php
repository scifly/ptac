<?php
namespace App\Models;

use App\Facades\Datatable;
use App\Helpers\{HttpStatusCode, ModelTrait, Snippet};
use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\{Builder, Model, Relations\HasOne};
use Illuminate\Http\JsonResponse;
use Illuminate\Support\{Carbon, Facades\DB, Facades\Request};
use Throwable;

/**
 * Class Face 人脸
 *
 * @package App\Models
 * @property int $id
 * @property string $faceid 人脸id
 * @property int $user_id 用户id
 * @property int $v_type 有效期类型：1 - 天，2 - 星期，3 - 时间
 * @property string $v_start 有效期开始时间
 * @property string $v_end 有效期结束时间
 * @property int $wgid 韦根id
 * @property string $url 人脸图片地址
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property int $state 状态: 1 - 白名单，2 - 黑名单，3 - vip
 * @property-read User $user
 * @method static Builder|Face newModelQuery()
 * @method static Builder|Face newQuery()
 * @method static Builder|Face query()
 * @method static Builder|Face whereCreatedAt($value)
 * @method static Builder|Face whereId($value)
 * @method static Builder|Face whereFaceid($value)
 * @method static Builder|Face whereState($value)
 * @method static Builder|Face whereUpdatedAt($value)
 * @method static Builder|Face whereUrl($value)
 * @method static Builder|Face whereUserId($value)
 * @method static Builder|Face whereVEnd($value)
 * @method static Builder|Face whereVStart($value)
 * @method static Builder|Face whereVType($value)
 * @method static Builder|Face whereWgid($value)
 * @mixin Eloquent
 */
class Face extends Model {
    
    use ModelTrait;
    
    protected $fillable = [
        'faceid', 'user_id', 'v_type', 'v_start',
        'v_end', 'wgid', 'url', 'state',
    ];
    
    /**
     * 返回人脸所属用户对象
     *
     * @return HasOne
     */
    function user() { return $this->hasOne('App\Models\User'); }
    
    /**
     * 人脸数据列表
     *
     * @return array
     */
    function index() {
        
        $columns = [
            ['db' => 'User.id', 'dt' => 0],
            [
                'db'        => 'Face.url', 'dt' => 1,
                'formatter' => function ($d) {
                    return isset($d) ? Snippet::avatar($d) : ' - ';
                },
            ],
            ['db' => 'User.realname', 'dt' => 2],
            ['db' => 'Groups.name', 'dt' => 3],
            [
                'db'        => 'Card.created_at', 'dt' => 4, 'dr' => true,
                'formatter' => function ($d) { return $d ?? ' - '; },
            ],
            [
                'db'        => 'Card.updated_at', 'dt' => 5, 'dr' => true,
                'formatter' => function ($d) { return $d ?? ' - '; },
            ],
            [
                'db'        => 'Face.state', 'dt' => 6,
                'formatter' => function ($d, $row) {
                    if (!isset($d)) return ' - ';
                    $colors = [
                        ['text-gray', '未设置'],
                        ['text-green', '白名单'],
                        ['text-red', '黑名单'],
                        ['text-orange', 'VIP'],
                    ];
                    $state = sprintf(
                        Snippet::BADGE,
                        $colors[$d ?? 0][0], $colors[$d ?? 0][1]
                    );
                    
                    return Datatable::status($state, $row, false);
                },
            ],
        ];
        $joins = [
            [
                'table'      => 'faces',
                'alias'      => 'Face',
                'type'       => 'LEFT',
                'conditions' => [
                    'Face.id = User.face_id',
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
        $condition = 'User.id IN (' . $this->visibleUserIds() . ')';
        
        return Datatable::simple(new User, $columns, $joins, $condition);
        
    }
    
    /**
     * 设置人脸识别 - (批量)设置、修改、清除
     *
     * @param User|null $user
     * @param bool $issue
     * @return bool|JsonResponse
     * @throws Throwable
     */
    function store(User $user = null, $issue = false) {
        
        try {
            DB::transaction(function () use ($user) {
                !$user ?: Request::merge(['faces' => [$user->id => Request::input('face')]]);
                $faces = Request::input('faces');
                $inserts = $replaces = $purges = $userIds = [];
                foreach ($faces as $userId => $face) {
                    if (!empty($face)) {
                        if (!$_face = Face::whereUserId($userId)->first()) {
                            $inserts[] = $face;
                            $userIds[] = $userId;
                        } else {
                            if ($_face->faceid != $face['faceid']) {
                                $this->exists($face['faceid']);
                                $replaces[$userId] = $face;
                            }
                        }
                    } else {
                        !User::find($userId)->face ?: $purges[] = $userId;
                    }
                }
                # 设置
                $this->insert($inserts ?? []);
                $faces = $this->whereIn('user_id', $userIds)->get();
                foreach ($faces as $face) {
                    $face->user->update(['face_id' => $face->id]);
                }
                # invoke api here
                # 修改
                Request::merge(['faces' => $replaces]);
                $this->modify();
                # 清除
                Request::merge(['ids' => $purges]);
                $this->remove();
            });
            
        } catch (Exception $e) {
            throw $e;
        }
        
        return !$issue ? true : response()->json([
            'title'   => '批量设置',
            'message' => __('messages.ok'),
        ]);
        
    }
    
    /**
     * 修改设置
     *
     * @return bool
     * @throws Throwable
     */
    function modify() {
        
        $faces = Request::input('faces');
        try {
            DB::transaction(function () use ($faces) {
                $inserts = [];
                foreach ($faces as $userId => $face) {
                    $user = User::find($userId);
                    Request::merge(['ids' => [$userId]]);
                    if ($faceid = $face['faceid']) {
                        if (!$user->face || $user->face->faceid != $faceid) {   # 设置 || 修改
                            $this->exists($faceid);
                            !$user->face ?: $this->remove(true); # 黑名单
                        } else {
                            !$face['state'] != 2 ?: $this->remove(true); # 黑名单
                        }
                        if ($user->face) {
                            $user->face->update($face);
                        } else {
                            Request::merge(['face' => $face]);
                            $this->store($user);
                        }
                    } else {
                        $this->remove();
                    }
                }
                # invoke api here
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
    /**
     * 清除人脸识别数据
     *
     * @param bool $soft
     * @return bool
     * @throws Throwable
     */
    function remove($soft = false) {
        
        try {
            DB::transaction(function () use ($soft) {
                $userIds = (Request::route('id') && stripos(Request::path(), 'delete') !== false)
                    ? [Request::route('id')]
                    : array_values(Request::input('ids'));
                foreach ($userIds as $userId) {
                    $face = User::find($userId)->face;
                    # invoke api here
                }
                if (!$soft) {
                    $this->whereIn('user_id', $userIds)->delete();
                    User::whereIn('id', $userIds)->update(['face_id' => 0]);
                }
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
    /**
     * 获取时间字符串(Ymd H:i:s, 星期X, H:i:s)
     *
     * @param $d
     * @param $row
     * @return false|string
     */
    /*private function range($d, $row) {
        
        switch ($row['v_type']) {
            case 0:
                return date('Ymd H:i:s', $d);
            case 1:
                return Constant::WEEK_DAYS[$d];
            case 2:
                return gmdate('H:i:s', $d);
            default:
                return 'n/a';
        }
        
    }*/
    
    /**
     * 判断卡号是否已存在
     *
     * @param $faceid
     */
    private function exists($faceid) {
        
        abort_if(
            $this->whereFaceid($faceid)->first() ? true : false,
            HttpStatusCode::NOT_ACCEPTABLE,
            __('faceid:' . $faceid . ' 已被使用')
        );
        
    }
    
}
