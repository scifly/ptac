<?php
namespace App\Models;

use App\Helpers\ModelTrait;
use Carbon\Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\{Builder, Relations\BelongsTo, Relations\Pivot};
use Throwable;

/**
 * App\Models\ModuleStudent 学生应用模块订阅
 *
 * @property int $id
 * @property int $module_id 应用模块id
 * @property int $student_id 学生id
 * @property Carbon|null $created_at 创建于
 * @property Carbon|null $updated_at 更新于
 * @property int|null $expired_at 到期时间
 * @method static Builder|ModuleStudent whereCreatedAt($value)
 * @method static Builder|ModuleStudent whereExpiredAt($value)
 * @method static Builder|ModuleStudent whereId($value)
 * @method static Builder|ModuleStudent whereModuleId($value)
 * @method static Builder|ModuleStudent whereStudentId($value)
 * @method static Builder|ModuleStudent whereUpdatedAt($value)
 * @method static Builder|ModuleStudent newModelQuery()
 * @method static Builder|ModuleStudent newQuery()
 * @method static Builder|ModuleStudent query()
 * @mixin Eloquent
 * @property-read Module $module
 * @property-read Student $student
 */
class ModuleStudent extends Pivot {
    
    use ModelTrait;
    
    protected $fillable = ['module_id', 'student_id', 'expired_at'];
    
    /** @return BelongsTo */
    function module() { return $this->belongsTo('App\Models\Module'); }
    
    /** @return BelongsTo */
    function student() { return $this->belongsTo('App\Models\Student'); }
    
    /**
     * @param null $id
     * @throws Throwable
     */
    function remove($id = null) {
        
        $this->purge($id);
        
    }
    
}