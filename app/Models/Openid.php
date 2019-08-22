<?php
namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\{Builder, Model, Relations\BelongsTo};
use Illuminate\Support\Carbon;

/**
 * Class Openid
 *
 * @package App\Models
 * @property int $id
 * @property int $user_id 用户ID
 * @property int $app_id 应用id
 * @property string $openid 用户微信openid
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read App $app
 * @property-read User $user
 * @method static Builder|Openid newModelQuery()
 * @method static Builder|Openid newQuery()
 * @method static Builder|Openid query()
 * @method static Builder|Openid whereAppId($value)
 * @method static Builder|Openid whereCreatedAt($value)
 * @method static Builder|Openid whereId($value)
 * @method static Builder|Openid whereOpenid($value)
 * @method static Builder|Openid whereUpdatedAt($value)
 * @method static Builder|Openid whereUserId($value)
 * @mixin Eloquent
 */
class Openid extends Model {

    protected $fillable = ['user_id', 'app_id', 'openid'];
    
    /**
     * 返回openid所属的公众号app对象
     *
     * @return BelongsTo
     */
    function app() { return $this->belongsTo('App\Models\App'); }
    
    /**
     * 返回openid对应的用户对象
     *
     * @return BelongsTo
     */
    function user() { return $this->belongsTo('App\Models\User'); }
    
}
