<?php
namespace App\Models;

use App\Helpers\ModelTrait;
use Carbon\Carbon;
use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * App\Models\AlertType 警告类型
 *
 * @property int $id
 * @property string $name 提前提醒的时间
 * @property string $english_name 提前提醒时间的英文名称
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $enabled
 * @method static Builder|AlertType whereCreatedAt($value)
 * @method static Builder|AlertType whereEnabled($value)
 * @method static Builder|AlertType whereEnglishName($value)
 * @method static Builder|AlertType whereId($value)
 * @method static Builder|AlertType whereName($value)
 * @method static Builder|AlertType whereUpdatedAt($value)
 * @method static Builder|AlertType newModelQuery()
 * @method static Builder|AlertType newQuery()
 * @method static Builder|AlertType query()
 * @mixin Eloquent
 */
class AlertType extends Model {
    
    use ModelTrait;
    
    protected $fillable = ['name', 'english_name', 'enabled'];
    
    /**
     * 删除警告类型
     *
     * @param null|$id
     * @return bool|null
     * @throws Throwable
     */
    function remove($id = null) {
    
        try {
            DB::transaction(function () use ($id) {
                $this->purge([class_basename($this)], 'id', 'purge', $id);
            });
        } catch (Exception $e) {
            throw $e;
        }
    
        return true;
        
    }
    
}
