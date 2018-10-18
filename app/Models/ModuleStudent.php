<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ModuleStudent 学生应用模块订阅
 */
class ModuleStudent extends Model {
    
    protected $table = 'modules_students';
    
    protected $fillable = ['module_id', 'student_id', 'expired_at'];
    
}