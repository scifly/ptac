<?php
namespace App\Models;

use App\Facades\Datatable;
use App\Helpers\ModelTrait;
use Carbon\Carbon;
use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * App\Models\Tag 标签
 *
 * @property int $id
 * @property string $name 标签名称
 * @property int $school_id 所属学校ID
 * @property string|null $remark 备注
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $enabled
 * @property-read Collection|Educator[] $educators
 * @property-read School $school
 * @method static Builder|Tag whereCreatedAt($value)
 * @method static Builder|Tag whereEnabled($value)
 * @method static Builder|Tag whereId($value)
 * @method static Builder|Tag whereName($value)
 * @method static Builder|Tag whereRemark($value)
 * @method static Builder|Tag whereSchoolId($value)
 * @method static Builder|Tag whereUpdatedAt($value)
 * @mixin Eloquent
 * @property-read Collection|\App\Models\User[] $users
 */
class Tag extends Model {
    
    use ModelTrait;
    
    protected $fillable = ['name', 'school_id', 'remark', 'enabled'];
    
    /**
     * 返回指定标签所属的学校对象
     *
     * @return BelongsTo
     */
    function school() { return $this->belongsTo('App\Models\School'); }
    
    /**
     * 获取指定标签包含的所有用户对象
     *
     * @return BelongsToMany
     */
    function users() { return $this->belongsToMany('App\Models\User', 'tags_users'); }
    
    /**
     * 标签列表
     *
     * @return array
     */
    function index() {
        
        $columns = [
            ['db' => 'Tag.id', 'dt' => 0],
            [
                'db' => 'Tag.name', 'dt' => 1,
                'formatter' => function ($d) {
                    return explode('.', $d)[0];
                }
            ],
            ['db' => 'Tag.remark', 'dt' => 2],
            ['db' => 'Tag.created_at', 'dt' => 3],
            ['db' => 'Tag.updated_at', 'dt' => 4],
            [
                'db'        => 'Tag.enabled', 'dt' => 5,
                'formatter' => function ($d, $row) {
                    return Datatable::dtOps($d, $row, false);
                },
            ],
        ];
        $condition = 'Tag.school_id = ' . $this->schoolId();
        
        return Datatable::simple(
            $this->getModel(), $columns, null, $condition
        );
        
    }
    
    /**
     * 保存标签
     *
     * @param array $data
     * @return bool
     */
    function store(array $data) {
        
        return $this->create($data) ? true : false;
        
    }
    
    /**
     * 更新标签
     *
     * @param array $data
     * @param $id
     * @return bool
     */
    function modify(array $data, $id) {
        
        return $this->find($id)->update($data);
        
    }
    
    /**
     * 删除标签
     *
     * @param $id
     * @return bool|null
     * @throws Throwable
     */
    function remove($id = null) {
        
        return $this->del($this, $id);
        
    }
    
    /**
     * 删除指定标签的所有数据
     *
     * @param $id
     * @return bool
     * @throws Throwable
     */
    function purge($id) {
        
        try {
            DB::transaction(function () use ($id) {
                TagUser::whereTagId($id)->delete();
                $this->find($id)->delete();
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
}
