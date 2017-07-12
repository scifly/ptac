<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\EducatorClass
 *
 * @property int $id
 * @property int $educator_id 教职员工ID
 * @property int $class_id 班级ID
 * @property int $subject_id 科目ID
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\EducatorClass whereClassId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\EducatorClass whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\EducatorClass whereEducatorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\EducatorClass whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\EducatorClass whereSubjectId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\EducatorClass whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class EducatorClass extends Model {
    
    protected $table = 'educators_classes';
    
}
