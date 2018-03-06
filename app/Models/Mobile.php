<?php

namespace App\Models;

use App\Helpers\ModelTrait;
use Carbon\Carbon;
use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    // todo: needs to be optimized

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
     * @param array $data
     * @return bool
     */
    function store(array $data) {
        
        $mobile = $this->create($data);

        return $mobile ? true : false;

    }

    /**
     * 更新手机号码
     *
     * @param array $data
     * @param $id
     * @return bool
     */
    function modify(array $data, $id) {
        
        $mobile = $this->find($id);
        if (!$mobile) { return false; }

        return $mobile->update($data) ? true : false;

    }
    
    /**
     * 删除手机号码
     *
     * @param $id
     * @return bool|null
     * @throws Exception
     */
    function remove($id) {
        
        $mobile = $this->find($id);
        if (!$mobile) { return false; }

        return $mobile->removable($mobile) ? $mobile->delete() : false;

    }

}
