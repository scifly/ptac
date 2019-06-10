<?php
namespace App\Models;

use App\Helpers\ModelTrait;
use Carbon\Carbon;
use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\{Builder, Relations\BelongsTo, Relations\Pivot};
use Illuminate\Support\{Facades\DB};
use Throwable;

/**
 * App\Models\ClassEducator 教职员工与班级关系
 *
 * @property int $id
 * @property int $educator_id 教职员工ID
 * @property int $class_id 班级ID
 * @property int $subject_id 科目ID
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $enabled 是否启用
 * @property-read Educator $educator
 * @property-read Squad $squad
 * @property-read Subject $subject
 * @property-read Educator $classes
 * @method static Builder|ClassEducator whereClassId($value)
 * @method static Builder|ClassEducator whereCreatedAt($value)
 * @method static Builder|ClassEducator whereEducatorId($value)
 * @method static Builder|ClassEducator whereId($value)
 * @method static Builder|ClassEducator whereSubjectId($value)
 * @method static Builder|ClassEducator whereUpdatedAt($value)
 * @method static Builder|ClassEducator whereEnabled($value)
 * @method static Builder|ClassEducator newModelQuery()
 * @method static Builder|ClassEducator newQuery()
 * @method static Builder|ClassEducator query()
 * @mixin Eloquent
 */
class ClassEducator extends Pivot {
    
    use ModelTrait;
    
    protected $fillable = ['educator_id', 'class_id', 'subject_id', 'enabled'];
    
    /**
     * 返回所属的教职员工对象
     *
     * @return BelongsTo
     */
    function classes() { return $this->belongsTo('App\Models\Educator'); }
    
    /**
     * 返回所属的班级对象
     *
     * @return BelongsTo
     */
    function squad() { return $this->belongsTo('App\Models\Squad', 'class_id', 'id'); }
    
    /**
     * 返回所属的科目对象
     *
     * @return BelongsTo
     */
    function subject() { return $this->belongsTo('App\Models\Subject'); }
    
    /**
     * 保存教职员工班级绑定关系
     *
     * @param array $data
     * @return bool
     */
    function store(array $data) {
        
        return $this->create($data) ? true : false;
        
    }
    
    /**
     * 保存指定教职员工的班级科目绑定关系
     *
     * @param $educatorId
     * @param array $data
     * @return bool
     * @throws Exception
     * @throws Throwable
     */
    function storeByEducatorId($educatorId, array $data) {
        
        try {
            DB::transaction(function () use ($educatorId, $data) {
                $educator = Educator::find($educatorId);
                if ($educator->user->role() != '学校') {
                    $this->where('educator_id', $educatorId)->delete();
                    $classIds = $data['class_ids'];
                    $subjectIds = $data['subject_ids'];
                    for ($i = 0; $i < sizeof($classIds); $i++) {
                        $record = array_combine(
                            $this->fillable,
                            [$educatorId, $classIds[$i], $subjectIds[$i], 1]
                        );
                        $this->where($record)->first() ?: $this->create($record);
                    }
                }
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
    /**
     * 更新教职员工班级绑定关系
     *
     * @param array $data
     * @param $id
     * @return bool
     */
    function modify(array $data, $id) {
        
        return $this->find($id)->update($data);
        
    }
    
    /**
     * 删除教职员工班级绑定关系
     *
     * @param null $id
     * @return bool|null
     * @throws Throwable
     */
    function remove($id = null) {
        
        return $this->purge([class_basename($this)], 'id', 'purge', $id);
        
    }
    
}
