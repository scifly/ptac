<?php
namespace App\Models;

use App\Helpers\ModelTrait;
use Carbon\Carbon;
use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * App\Models\CommType 通信方式
 *
 * @property int $id
 * @property string $name 通信方式名称
 * @property string $remark 通信方式备注
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $enabled
 * @property-read Collection|Message[] $messages
 * @method static Builder|CommType whereCreatedAt($value)
 * @method static Builder|CommType whereEnabled($value)
 * @method static Builder|CommType whereId($value)
 * @method static Builder|CommType whereName($value)
 * @method static Builder|CommType whereRemark($value)
 * @method static Builder|CommType whereUpdatedAt($value)
 * @method static Builder|CommType newModelQuery()
 * @method static Builder|CommType newQuery()
 * @method static Builder|CommType query()
 * @mixin Eloquent
 */
class CommType extends Model {
    
    use ModelTrait;
    
    protected $table = 'comm_types';
    
    protected $fillable = ['name', 'remark', 'enabled'];
    
    /**
     * 返回指定通信方式包含的所有消息对象
     *
     * @return HasMany
     */
    function messages() { return $this->hasMany('App\Models\Message'); }
    
    /**
     * 移除通信类型
     *
     * @param $id
     * @return bool|null
     * @throws Throwable
     */
    function remove($id = null) {
    
        try {
            DB::transaction(function () use ($id) {
                $this->purge([class_basename($this)], 'id', 'purge', $id);
                $this->purge(['Message'], 'comm_type_id', 'reset', $id);
            });
        } catch (Exception $e) {
            throw $e;
        }
    
        return true;
        
    }
    
}
