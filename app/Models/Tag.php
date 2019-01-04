<?php
namespace App\Models;

use App\Facades\Datatable;
use App\Helpers\Constant;
use App\Helpers\ModelTrait;
use App\Jobs\SyncTag;
use Carbon\Carbon;
use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * App\Models\Tag 标签
 *
 * @property int $id
 * @property string $name 标签名称
 * @property int $school_id 所属学校ID
 * @property int $user_id 创建者的用户id
 * @property string|null $remark 备注
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $enabled
 * @property int $synced 同步状态
 * @property-read Collection|User[] $users
 * @property-read School $school
 * @property-read User $creator
 * @property-read Collection|Department[] $departments
 * @method static Builder|Tag whereCreatedAt($value)
 * @method static Builder|Tag whereEnabled($value)
 * @method static Builder|Tag whereId($value)
 * @method static Builder|Tag whereName($value)
 * @method static Builder|Tag whereRemark($value)
 * @method static Builder|Tag whereSchoolId($value)
 * @method static Builder|Tag whereUserId($value)
 * @method static Builder|Tag whereUpdatedAt($value)
 * @method static Builder|Tag whereSynced($value)
 * @method static Builder|Tag newModelQuery()
 * @method static Builder|Tag newQuery()
 * @method static Builder|Tag query()
 * @mixin Eloquent
 */
class Tag extends Model {
    
    use ModelTrait;
    
    protected $fillable = [
        'name', 'school_id', 'user_id',
        'remark', 'enabled', 'synced'
    ];
    
    /**
     * 返回指定标签所属的学校对象
     *
     * @return BelongsTo
     */
    function school() { return $this->belongsTo('App\Models\School'); }
    
    /**
     * 返回指定标签的创建者用户对象
     *
     * @return BelongsTo
     */
    function creator() { return $this->belongsTo('App\Models\User'); }
    
    /**
     * 获取指定标签包含的所有用户对象
     *
     * @return BelongsToMany
     */
    function users() { return $this->belongsToMany('App\Models\User', 'tags_users'); }
    
    /**
     * 获取指定标签包含的所有部门对象
     *
     * @return BelongsToMany
     */
    function departments() { return $this->belongsToMany('App\Models\Department', 'departments_tags'); }
    
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
                'db' => 'Tag.synced', 'dt' => 5,
                'formatter' => function ($d) {
                    return $this->synced($d);
                }
            ],
            [
                'db'        => 'Tag.enabled', 'dt' => 6,
                'formatter' => function ($d, $row) {
                    return Datatable::status($d, $row, false);
                },
            ],
        ];
        $condition = 'Tag.school_id = ' . $this->schoolId();
        # 非超级用户(运营、企业、学校)只能查看编辑自己创建的标签
        if (!in_array(Auth::user()->role(), Constant::SUPER_ROLES)) {
            $condition .= ' AND Tag.user_id = ' . Auth::id();
        }
        
        return Datatable::simple(
            $this->getModel(), $columns, null, $condition
        );
        
    }
    
    /**
     * 保存标签
     *
     * @param array $data
     * @return bool
     * @throws Throwable
     */
    function store(array $data) {
        
        try {
            DB::transaction(function () use ($data) {
                $tag = $this->create($data);
                if (isset($data['user_ids'])) {
                    (new TagUser)->storeByTagId($tag->id, $data['user_ids']);
                }
                if (isset($data['dept_ids'])) {
                    (new DepartmentTag)->storeByTagId($tag->id, $data['dept_ids']);
                }
                if ($tag) {
                    $this->sync($tag->id, 'create');
                }
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
    /**
     * 更新标签
     *
     * @param array $data
     * @param $id
     * @return bool
     * @throws Throwable
     */
    function modify(array $data, $id) {
        
        try {
            DB::transaction(function () use ($data, $id) {
                $tag = $this->find($id);
                TagUser::whereTagId($tag->id)->delete();
                DepartmentTag::whereTagId($tag->id)->delete();
                if (isset($data['user_ids'])) {
                    (new TagUser)->storeByTagId($id, $data['user_ids']);
                }
                if (isset($data['dept_ids'])) {
                    (new DepartmentTag)->storeByTagId($tag->id, $data['dept_ids']);
                }
                $updated = $tag->update($data);
                if ($updated) { $this->sync($id, 'update'); }
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
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
                DepartmentTag::whereTagId($id)->delete();
                $this->sync($id, 'delete');
                $this->find($id)->delete();
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
    /**
     * 同步企业微信通绪论标签
     *
     * @param $id
     * @param $action
     */
    private function sync($id, $action) {
        
        $tag = $this->find($id);
        $data = [
            'tagid' => $id, 'corp_id' => $tag->school->corp_id
        ];
        if ($action != 'delete') {
            $data['tagname'] = $tag->name;
        }
        SyncTag::dispatch($data, Auth::id(), $action);
        
    }
    
}
