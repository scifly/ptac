<?php

namespace App\Models;

use App\Http\Requests\EducatorRequest;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Request;
use Throwable;

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
    function classes() { return $this->belongsTo('App\Models\Educator'); }
  
    function squad() { return $this->belongsTo('App\Models\squad', 'class_id', 'id'); }
    
    function subject() { return $this->belongsTo('App\Models\subject'); }
    
    /**
     * 保存教职员工班级绑定关系
     *
     * @param array $data
     * @return bool
     */
    function store(array $data) {
        
        return $this->create($data) ? true : false;
        
    }
    
    function modify(array $data, $id) {
        
        return $this->find($id)->update($data);
        
    }
    
    /**
     * 删除教职员工班级绑定关系
     *
     * @param null $id
     * @return bool|null
     * @throws Exception
     */
    function remove($id = null) {
        
        return $id
            ? $this->find($id)->delete()
            : $this->whereIn('id', array_values(Request::input('ids')))->delete();
        
    }
    
    /**
     * 删除指定班级的教职员工班级绑定关系
     *
     * @param $classId
     * @return bool|null
     * @throws Exception
     * @throws Throwable
     */
    function removeByClassId($classId) {
    
        $ecs = $this->where('class_id', $classId)->get();
        $educatorIds = array_unique($ecs->pluck('educator_id')->toArray());
        $request = new EducatorRequest();
        $request->merge(['ids' => $educatorIds]);
        (new Educator)->modify($request);
    
        return $this->where('class_id', $classId)->delete();
    
    }
    
}
