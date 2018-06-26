<?php
namespace App\Models;

use App\Helpers\ModelTrait;
use Carbon\Carbon;
use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;

/**
 * App\Models\Mobile 手机号码
 *
 * @property int $id
 * @property int $user_id 手机号码所属用户ID
 * @property string $mobile 手机号码
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $enabled
 * @property int $isdefault 是否为默认的手机号码
 * @property-read User $user
 * @method static Builder|Mobile whereCreatedAt($value)
 * @method static Builder|Mobile whereEnabled($value)
 * @method static Builder|Mobile whereId($value)
 * @method static Builder|Mobile whereIsdefault($value)
 * @method static Builder|Mobile whereMobile($value)
 * @method static Builder|Mobile whereUpdatedAt($value)
 * @method static Builder|Mobile whereUserId($value)
 * @mixin Eloquent
 */
class Mobile extends Model {
    
    use ModelTrait;
    
    protected $fillable = ['mobile', 'user_id', 'isdefault', 'enabled'];
    
    /**
     * 返回指定手机所属的用户对象
     *
     * @return BelongsTo
     */
    function user() { return $this->belongsTo('App\Models\User'); }
    
    /**
     * 保存手机号码
     *
     * @param array $mobiles
     * @param User $user
     * @return bool
     * @throws Exception
     */
    function store(array $mobiles, User $user) {
        
        try {
            DB::transaction(function () use ($mobiles, $user) {
                self::whereUserId($user->id)->delete();
                $records = [];
                foreach ($mobiles as $mobile) {
                    $records[] = [
                        'user_id'   => $user->id,
                        'mobile'    => $mobile['mobile'],
                        'isdefault' => $mobile['isdefault'],
                        'enabled'   => $mobile['enabled'],
                    ];
                }
                $this->insert($records);
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
    /**
     * 更新手机号码
     *
     * @param array $data
     * @param $id
     * @return bool
     */
    function modify(array $data, $id = null) {
        
        return $id
            ? $this->find($id)->update($data)
            : $this->batch($this);
        
    }
    
    /**
     * 删除手机号码
     *
     * @param $id
     * @return bool|null
     * @throws Exception
     */
    function remove($id = null) {
        
        return $id
            ? $this->find($id)->delete()
            : $this->whereIn('id', array_values(Request::input('ids')))->delete();
        
    }
    
}
