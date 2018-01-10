<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\EducatorClass 教职员工与班级关系
 *
 * @property int $id
 * @property int $educator_id 教职员工ID
 * @property int $class_id 班级ID
 * @property int $subject_id 科目ID
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property int $enabled 是否启用
 * @method static Builder|EducatorClass whereClassId($value)
 * @method static Builder|EducatorClass whereCreatedAt($value)
 * @method static Builder|EducatorClass whereEducatorId($value)
 * @method static Builder|EducatorClass whereId($value)
 * @method static Builder|EducatorClass whereSubjectId($value)
 * @method static Builder|EducatorClass whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read Educator $educator
 * @property-read Squad $squad
 * @property-read Subject $subject
 * @method static Builder|EducatorClass whereEnabled($value)
 * @property-read Educator $classes
 */
class EducatorClass extends Model {

    protected $table = 'educators_classes';

    protected $fillable = ['educator_id', 'class_id', 'subject_id', 'enabled'];
    
    /**
     * 返回所属的教职员工对象
     *
     * @return BelongsTo
     */
    public function classes() { return $this->belongsTo('App\Models\Educator'); }
  
    public function squad() { return $this->belongsTo('App\Models\squad', 'class_id', 'id'); }
    
    public function subject() { return $this->belongsTo('App\Models\subject'); }

}
