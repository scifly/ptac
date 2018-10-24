<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ModuleStudent 学生应用模块订阅
 *
 * @property int $id
 * @property int $module_id 应用模块id
 * @property int $student_id 学生id
 * @property \Carbon\Carbon|null $created_at 创建于
 * @property \Carbon\Carbon|null $updated_at 更新于
 * @property int|null $expired_at 到期时间
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ModuleStudent whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ModuleStudent whereExpiredAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ModuleStudent whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ModuleStudent whereModuleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ModuleStudent whereStudentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ModuleStudent whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ModuleStudent extends Model {
    
    protected $table = 'modules_students';
    
    protected $fillable = ['module_id', 'student_id', 'expired_at'];
    
}