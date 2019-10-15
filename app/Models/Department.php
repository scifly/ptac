<?php
namespace App\Models;

use App\Helpers\{ModelTrait};
use App\Jobs\SyncDepartment;
use Carbon\Carbon;
use Eloquent;
use Exception;
use Html;
use Illuminate\Database\Eloquent\{Builder,
    Collection,
    Model,
    Relations\BelongsTo,
    Relations\BelongsToMany,
    Relations\HasMany,
    Relations\HasOne};
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection as SCollection;
use Illuminate\Support\Facades\{Auth, DB, Request};
use ReflectionException;
use Throwable;

/**
 * App\Models\Department 部门
 *
 * @property int $id
 * @property int|null $parent_id 父部门ID
 * @property string $name 部门名称
 * @property string|null $remark 部门备注
 * @property int|null $order 在父部门中的次序值。order值大的排序靠前
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $enabled
 * @property int $department_type_id 所属部门类型ID
 * @property int|null $synced 是否已同步到企业微信通讯录
 * @property-read Collection|Department[] $children
 * @property-read Company $company
 * @property-read Corp $corp
 * @property-read DepartmentType $dType
 * @property-read Grade $grade
 * @property-read Department|null $parent
 * @property-read School $school
 * @property-read Squad $squad
 * @property-read Collection|User[] $users
 * @property-read Collection|Tag[] $tags
 * @property-read int|null $children_count
 * @property-read int|null $tags_count
 * @property-read int|null $users_count
 * @method static Builder|Department whereCreatedAt($value)
 * @method static Builder|Department whereDepartmentTypeId($value)
 * @method static Builder|Department whereEnabled($value)
 * @method static Builder|Department whereId($value)
 * @method static Builder|Department whereName($value)
 * @method static Builder|Department whereOrder($value)
 * @method static Builder|Department whereParentId($value)
 * @method static Builder|Department whereRemark($value)
 * @method static Builder|Department whereUpdatedAt($value)
 * @method static Builder|Department whereSynced($value)
 * @method static Builder|Department newModelQuery()
 * @method static Builder|Department newQuery()
 * @method static Builder|Department query()
 * @mixin Eloquent
 */
class Department extends Model {
    
    use ModelTrait;
    
    protected $fillable = [
        'parent_id', 'department_type_id', 'name',
        'remark', 'order', 'enabled', 'synced',
    ];
    
    /** properties -------------------------------------------------------------------------------------------------- */
    /** @return BelongsTo */
    function dType() { return $this->belongsTo('App\Models\DepartmentType', 'department_type_id'); }
    
    /** @return HasOne */
    function company() { return $this->hasOne('App\Models\Company'); }
    
    /** @return HasOne */
    function corp() { return $this->hasOne('App\Models\Corp'); }
    
    /** @return HasOne */
    function school() { return $this->hasOne('App\Models\School'); }
    
    /** @return HasOne */
    function grade() { return $this->hasOne('App\Models\Grade'); }
    
    /** @return HasOne */
    function squad() { return $this->hasOne('App\Models\Squad'); }
    
    /** @return BelongsToMany */
    function users() { return $this->belongsToMany('App\Models\User', 'department_user'); }
    
    /** @return BelongsToMany */
    function tags() { return $this->belongsToMany('App\Models\Tag', 'department_tag'); }
    
    /** @return BelongsTo */
    function parent() { return $this->belongsTo('App\Models\Department', 'parent_id'); }
    
    /** @return HasMany */
    function children() { return $this->hasMany('App\Models\Department', 'parent_id', 'id'); }
    
    /** crud -------------------------------------------------------------------------------------------------------- */
    /**
     * 部门列表/排序/移动
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    function index() {
        
        try {
            $action = Request::input('action');
            if ($action == 'tree') {
                $response = $this->tree(false, false);
            } elseif ($action == 'sort') {
                $this->sort();
            } else {
                $this->move();
            }
        } catch (Exception $e) {
            throw $e;
        }
        
        return $response ?? response()->json();
        
    }
    
    /**
     * 创建部门
     *
     * @param array $data
     * @return bool
     * @throws Throwable
     */
    function store(array $data) {
        
        try {
            DB::transaction(function () use ($data) {
                $dept = $this->create($data);
                if (isset($data['tag_ids'])) {
                    (new DepartmentTag)->storeByDeptId($dept->id, $data['tag_ids']);
                }
                if ($this->needSync($dept)) {
                    SyncDepartment::dispatch([$dept->id], 'create', Auth::id());
                }
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
    /**
     * 创建非'其他'类型部门
     *
     * @param Model $model
     * @param null $belongsTo
     * @return Department|bool|Model
     * @throws Throwable
     */
    function stow(Model $model, $belongsTo = null) {
        
        $dept = null;
        try {
            DB::transaction(function () use ($model, $belongsTo, &$dept) {
                $dType = DepartmentType::whereRemark(lcfirst(class_basename($model)))->first();
                $dept = $this->create([
                    'parent_id'          => $belongsTo
                        ? $model->{$belongsTo}->department_id
                        : $this::whereParentId(null)->first()->id,
                    'name'               => $model->{'name'},
                    'remark'             => $model->{'remark'},
                    'department_type_id' => $dType->id,
                    'order'              => $this->all()->max('order') + 1,
                    'enabled'            => $model->{'enabled'},
                ]);
                # 创建年级/班级主任用户与部门的绑定关系
                $this->updateDu($dType->name, $model, $dept);
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return $dept;
        
    }
    
    /**
     * 更新部门
     *
     * @param array $data
     * @param $id
     * @return bool
     * @throws Throwable
     */
    function modify(array $data, $id) {
        
        return $this->revise(
            $this, $data, $id,
            function (Department $dept) use ($data, $id) {
                if (isset($data['tag_ids'])) {
                    (new DepartmentTag)->storeByDeptId($dept->id, $data['tag_ids']);
                }
                if ($this->needSync($dept)) {
                    SyncDepartment::dispatch([$id], 'update', Auth::id());
                }
            }
        );

    }
    
    /**
     * 更新非'其他'类型部门
     *
     * @param Model $model
     * @param null $beLongsTo
     * @return void
     * @throws Throwable
     */
    function alter(Model $model, $beLongsTo = null) {
        
        try {
            DB::transaction(function () use ($model, $beLongsTo) {
                $dType = DepartmentType::whereRemark(lcfirst(class_basename($model)))->first();
                $data = [
                    'name'               => $model->{'name'},
                    'remark'             => $model->{'remark'},
                    'department_type_id' => $dType->id,
                    'enabled'            => $model->{'enabled'},
                ];
                /**
                 * 如果部门类型为年级或班级，则不更新其父部门id，
                 * 因为年级或班级可能是其他类型部门的子部门
                 */
                if ($beLongsTo && in_array($beLongsTo, ['company', 'corp'])) {
                    $data['parent_id'] = $beLongsTo
                        ? $model->{$beLongsTo}->department_id
                        : $this::whereParentId(null)->first()->id;
                }
                ($dept = $this->find($model->{'department_id'}))->update($data);
                $this->updateDu($dType->name, $model, $dept);
            });
        } catch (Exception $e) {
            throw $e;
        }
        
    }
    
    /**
     * 删除部门
     *
     * @param $id
     * @return bool|null
     * @throws Exception
     * @throws Throwable
     */
    function remove($id) {
        
        try {
            throw_if(
                !$dept = $this->find($id),
                new Exception(__('messages.not_found'))
            );
            throw_if(
                $dept->children->isNotEmpty(),
                new Exception(__('messages.department.has_children'))
            );
            throw_if(
                $dept->dType->name != '其他',
                new Exception(__('messages.department.forbidden'))
            );
            $this->purge($id, [
                'purge.department_id' => ['DepartmentUser', 'DepartmentTag'],
            ]);
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
    /** Helper functions -------------------------------------------------------------------------------------------- */
    /**
     * 获取联系人树
     *
     * @param bool $contact - true：获取联系人
     * @param bool $direct - true: 获取校级树
     * @return array|JsonResponse
     * @throws Throwable
     */
    function tree($contact = true, $direct = true) {
        
        $rootId = $this->rootId($direct);
        $ids = collect([$rootId])->merge($this->subIds($rootId));
        $depts = $this->orderBy('order')->whereIn('id', $ids)->get();
        $firstId = $depts->first()->id;
        $nodes = [];
        $fields = ['id', 'parent', 'text', 'selectable', 'type'];
        /** @var Department $dept */
        foreach ($depts as $dept) {
            $dt = $dept->dType;
            $text = Html::tag('span', $dept->name, [
                'class' => $dept->enabled ? $dt->color : 'text-gray',
                'title' => $title ?? '',
            ])->toHtml();
            $nodes[] = array_merge(
                array_combine($fields, [
                    $id = $dept->id,
                    $firstId == $id ? '#' : $dept->parent_id,
                    $text, 1, $type = $dt->remark
                ]),
                ['corp_id' => !in_array($type, ['root', 'company']) ? $this->corpId($id) : null]
            );
        }
        if ($contact) {
            # 获取可见部门下的学生、教职员工 & 不可见部门下的教职员工
            $visibleIds = $this->departmentIds()->flip();
            foreach ($nodes as $node) {
                $deptId = $node['id'];
                foreach ($this->find($deptId)->users as $user) {
                    if ($user->educator || $visibleIds->has($deptId) && $user->student) {
                        $contacts[] = array_combine($fields, [
                            'user-' . $deptId . '-' . $user->id,
                            $deptId, $user->realname, 1, 'user',
                        ]);
                    }
                }
            }
        }
        
        return response()->json(
            array_merge($nodes, $contacts ?? [])
        );
        
    }
    
    /**
     * @return array
     * @throws Exception
     */
    function compose() {
        
        return explode('/', Request::path())[1] == 'index'
            ? []
            : (new Tag)->compose(
                'department', $this->find(Request::route('id'))
            );
        
    }
    
    /**
     * 判断指定部门是否需要同步到企业微信
     *
     * @param Department|null $dept
     * @return bool
     */
    function needSync(Department $dept = null) {
        
        return !$dept ? false : !in_array(
            $dept->dType->name, ['根', '运营', '企业']
        );
        
    }
    
    /**
     * 返回指定部门(含子部门）下的所有用户id
     *
     * @param $id
     * @param null $role
     * @return SCollection
     * @throws ReflectionException
     */
    function userIds($id, $role = null) {
        
        $deptIds = collect([$id])->merge($this->subIds($id));
        $userIds = DepartmentUser::whereIn('department_id', $deptIds)->pluck('user_id');
        !$role ?: $userIds = $userIds->intersect($this->model($role)->pluck('user_id'));
        
        return $userIds->unique();
        
    }
    
    /**
     * 返回指定部门所处的级别
     *
     * @param integer $id 部门ID
     * @param integer $level 部门所处级别
     * @return int|null
     */
    function level($id, &$level) {
        
        if (!($dept = $this->find($id))) return null;
        if ($parent = $dept->parent) {
            $level += 1;
            $this->level($parent->id, $level);
        }
        
        return $level;
        
    }
    
    /**
     * 返回指定部门的所有上级（校级及以下）部门id
     *
     * @param integer $id
     * @param array $ids
     * @return array
     */
    function parentIds($id, $ids = []): array {
        
        $dept = $this->find($id);
        if ($dept->dType->name != '学校') {
            $ids[] = $dept->parent_id;
            $ids = $this->parentIds($dept->parent_id, $ids);
        }
        
        return $ids;
        
    }
    
    /**
     * 保存部门排序
     *
     * @throws Exception
     */
    private function sort() {
        
        try {
            $orders = Request::get('data');
            $originalOrders = $this->orderBy('order')
                ->whereIn('id', array_keys($orders))
                ->get()->pluck('order', 'id')->toArray();
            foreach ($orders as $id => $order) {
                $originalOrder = array_slice(
                    $originalOrders, $order, 1, true
                );
                $this->find($id)->update([
                    'order' => $originalOrder[key($originalOrder)],
                ]);
            };
        } catch (Exception $e) {
            throw $e;
        }
        
    }
    
    /**
     * 更改部门所处位置
     *
     * @return bool
     * @throws Throwable
     */
    private function move() {
        
        try {
            DB::transaction(function () {
                $id = Request::input('id');
                $pId = Request::input('parentId');
                throw_if(
                    !isset($id, $pId) || !$this->find($id) || !$this->find($pId) ||
                    collect([$id, $pId])->intersect($this->departmentIds(Auth::id()))->count() < 2,
                    __('messages.forbidden')
                );
                [$type, $pType] = array_map(
                    function ($id) {
                        return $this->find($id)->dType->name;
                    }, [$id, $pId]
                );
                $relations = [
                    '运营' => '根',
                    '企业' => '运营',
                    '学校' => ['企业'],
                    '年级' => ['学校', '其他'],
                    '班级' => ['年级', '其他'],
                    '其他' => ['运营', '企业']
                ];
                $parents = $relations[$type];
                $movable = in_array($pType, is_array($parents) ? $parents : [$parents])
                    && (is_array($parents) ? $this->corpId($id) == $this->corpId($pId) : true);
                throw_if(!$movable, new Exception(__('messages.forbidden')));
                $dept = $this->find($id);
                $dept->parent_id = $pId === '#' ? null : intval($pId);
                throw_if(!$dept->save(), new Exception(__('messages.fail')));
                # 更新部门对应企业/学校/年级/班级、菜单等对象
                $dType = $dept->dType->name;
                if ($dType == '企业') {
                    $corp = $this->find($id)->corp;
                    $company = $this->find($pId)->company;
                    $corp->update(['company_id' => $company->id]);
                    Menu::find($corp->menu_id)->update([
                        'parent_id' => Menu::find($company->menu_id)->first()->id,
                    ]);
                } elseif ($dType == '年级') {
                    $grade = $this->find($id)->grade;
                    $parent = $this->find($pId);
                    if ($parent->dType->name == '学校') {
                        $grade->update(['school_id' => $parent->school->id]);
                    }
                } else { # '班级':
                    $class = Squad::whereDepartmentId($id)->first();
                    $parent = $this->find($pId);
                    if ($parent->dType->name == '年级') {
                        $class->update(['grade_id' => $parent->grade->id]);
                    }
                }
                !$dept->needSync($dept) ?: SyncDepartment::dispatch([$id], 'update', Auth::id());
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
    /**
     * 更新年级/班级主任与部门的绑定关系
     *
     * @param $dtType
     * @param $model
     * @param $dept
     * @throws Throwable
     */
    private function updateDu($dtType, $model, $dept): void {
        
        if (in_array($dtType, ['grade', 'squad'])) {
            $educatorIds = explode(',', $model->{'educator_ids'});
            $users = User::with('educator')->whereIn('educator.id', $educatorIds)->get();
            if ($users->isNotEmpty() && $dept) {
                (new DepartmentUser)->storeByDeptId(
                    $dept->id, $users->pluck('user.id')
                );
            }
        }
        
    }
    
}
