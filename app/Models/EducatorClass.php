<?php
namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;
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
                if ($educator->user->group->name != '学校') {
                    $this->where('educator_id', $educatorId)->delete();
                    $classIds = $data['class_ids'];
                    $subjectIds = $data['subject_ids'];
                    for ($i = 0; $i < sizeof($classIds); $i++) {
                        $ec = $this->where([
                            'educator_id' => $educatorId,
                            'class_id' => $classIds[$i],
                            'subject_id' => $subjectIds[$i]
                        ])->first();
                        if (!$ec) {
                            $this->create([
                                'educator_id' => $educatorId,
                                'class_id' => $classIds[$i],
                                'subject_id' => $subjectIds[$i],
                                'enabled' => 1
                            ]);
                        }
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
     * @throws Exception
     */
    function remove($id = null) {
        
        return $id
            ? $this->find($id)->delete()
            : $this->whereIn('id', array_values(Request::input('ids')))->delete();
        
    }
    
}
