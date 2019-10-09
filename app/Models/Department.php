<?php
namespace App\Models;

use App\Helpers\{Constant, ModelTrait};
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
use Illuminate\Database\Query\Builder as QBuilder;
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
                $response = response()->json($this->tree());
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
        // try {
        //     DB::transaction(function () use ($data, $id) {
        //         throw_if(
        //             !$dept = $this->find($id),
        //             new Exception(__('messages.not_found'))
        //         );
        //         $dept->update($data);
        //         if (isset($data['tag_ids'])) {
        //             (new DepartmentTag)->storeByDeptId($dept->id, $data['tag_ids']);
        //         }
        //         if ($this->needSync($dept)) {
        //             SyncDepartment::dispatch([$id], 'update', Auth::id());
        //         }
        //     });
        // } catch (Exception $e) {
        //     throw $e;
        // }
        //
        // return true;
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
     * 返回所有叶节点部门
     *
     * @return array
     * @throws Exception
     */
    function leaves() {
        
        $leaves = [];
        $leafPath = [];
        $depts = $this->nodes();
        /** @var Department $dept */
        foreach ($depts as $dept) {
            if (empty($dept->children->count())) {
                $path = self::leafPath($dept->id, $leafPath);
                $leaves[$dept->id] = $path;
                $leafPath = [];
            }
        }
        
        return $leaves;
        
    }
    
    /**
     * 获取指定部门的完整路径
     *
     * @param $id
     * @param array $path
     * @return string
     */
    function leafPath($id, array &$path) {
        
        $this->truncate();
        if (!($dept = $this->find($id))) return '';
        $path[] = $dept->name;
        !isset($dept->parent_id) ?: $this->leafPath(
            $dept->parent_id, $path
        );
        krsort($path);
        
        return join(' . ', $path);
        
    }
    
    /**
     * 获取联系人树
     *
     * @param bool $contact - 部门树是否包含部门中的联系人
     * @return array|JsonResponse
     * @throws Exception
     */
    function contacts($contact = true) {
        
        $user = Auth::user();
        $role = $user->role();
        # 如果角色为教职员工且需要返回联系人，则返回整棵树，
        # 但不可见部门仅返回教职员工类联系人。
        $mixed = $contact && !in_array($role, Constant::NON_EDUCATOR);
        $contacts = [];
        if (in_array($role, Constant::SUPER_ROLES) || $mixed) {
            $id = School::find($this->schoolId())->department_id;
            $visibleNodes = $this->tree($id);
        } else {
            $nodes = $this->tree();
            # 当前用户可访问的所有部门id
            $allowedDeptIds = $this->departmentIds($user->id);
            # 当前用户可访问部门的所有上级部门id
            $allowedParentIds = [];
            foreach ($allowedDeptIds as $id) {
                $allowedParentIds[$id] = $this->parentIds($id);
            }
            # 对当前用户可见的所有部门节点
            $visibleNodes = [];
            foreach ($nodes as $node) {
                if (!$node['selectable']) {
                    foreach ($allowedParentIds as $id => $parentIds) {
                        if (in_array($node['id'], $parentIds)) {
                            $visibleNodes[] = $node;
                            break;
                        }
                    }
                } else {
                    $visibleNodes[] = $node;
                }
            }
        }
        if ($contact) {
            # 获取可见部门下的学生、教职员工 & 不可见部门下的教职员工
            $visibleIds = $mixed ? $this->departmentIds($user->id) : [];
            foreach ($visibleNodes as $node) {
                if ($node['selectable']) {
                    $users = $this->find($node['id'])->users->filter(
                        function (User $user) use ($node, $visibleIds) {
                            return (empty($visibleIds) || in_array($node['id'], $visibleIds))
                                ? true : !in_array($user->role(), Constant::NON_EDUCATOR);
                        }
                    );
                    /*$this->find($node['id'])->*/
                    $users->each(
                        function (User $user) use ($node, &$contacts) {
                            if ($user->student || $user->educator) {
                                $contacts[] = [
                                    'id'         => 'user-' . $node['id'] . '-' . $user->id,
                                    'parent'     => $node['id'],
                                    'text'       => $user->realname,
                                    'selectable' => 1,
                                    'type'       => 'user',
                                ];
                            }
                        }
                    );
                }
            }
        }
        
        return response()->json(
            array_merge($visibleNodes, $contacts)
        );
        
    }
    
    /**
     * 返回指定部门父级部门中类型为$type的部门id
     *
     * @param $id
     * @param string $type
     * @return int|mixed
     */
    function deptId($id, $type = '学校') {
        
        if (!$dept = $this->find($id)) return null;
        while ($dept->dType->name != $type) {
            if (!$dept = $dept->parent) return null;
        }
        
        return $dept->id;
        
    }
    
    /**
     * 返回指定部门(含子部门）下的所有用户id
     *
     * @param $id
     * @param null $type
     * @return SCollection
     * @throws ReflectionException
     */
    function userIds($id, $type = null) {
        
        $builder = DepartmentUser::whereIn(
            'department_id',
            collect([$id])->merge($this->subIds($id))
        );
        if ($type) {
            /** @var QBuilder $builder */
            $builder = $this->model($type)->whereIn(
                'user_id', $builder->pluck('user_id')
            );
        }
        
        return $builder->pluck('user_id')->unique();
        
    }
    
    /**
     * 返回View所需数据
     *
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
     * 获取用于显示jstree的部门数据
     *
     * @param null $rootId
     * @return array
     * @throws Exception
     */
    private function tree($rootId = null) {
        
        $user = Auth::user();
        $isSuperRole = in_array($user->role(), Constant::SUPER_ROLES);
        isset($rootId) ?: $rootId = $isSuperRole ? $this->rootId(true) : $this->topId();
        $depts = $this->nodes($rootId);
        $allowedIds = $this->departmentIds($user->id)->flip();
        $nodes = [];
        for ($i = 0; $i < sizeof($depts); $i++) {
            $id = $depts[$i]['id'];
            $parentId = $i == 0 ? '#' : $depts[$i]['parent_id'];
            $dt = DepartmentType::find($depts[$i]['department_type_id']);
            $name = $depts[$i]['name'];
            $enabled = $depts[$i]['enabled'];
            $type = $dt->remark;
            if (!in_array($type, ['root', 'company', 'corp'])) {
                $synced = $depts[$i]['synced'];
                $title = $synced ? '已同步' : '未同步';
                $syncMark = Html::tag('span', '*', [
                    'class' => 'text-' . ($synced ? 'green' : 'red'),
                ])->toHtml();
            }
            $text = Html::tag('span', $name, [
                    'class' => $enabled ? $dt->color : 'text-gray',
                    'title' => $title ?? '',
                ])->toHtml() . ($syncMark ?? '');
            $selectable = $isSuperRole ? 1 : ($allowedIds->has($id) ? 1 : 0);
            $corp_id = !in_array($type, ['root', 'company'])
                ? $this->corpId($id) : null;
            $nodes[] = [
                'id'         => $id,
                'parent'     => $parentId,
                'text'       => $text,
                'type'       => $type,
                'selectable' => $selectable,
                'corp_id'    => $corp_id,
            ];
        }
        
        return $nodes;
        
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
                $parentId = Request::input('parentId');
                throw_if(
                    !isset($id, $parentId) ||
                    !$this->find($id) || !$this->find($parentId) ||
                    collect([$id, $parentId])->intersect($this->departmentIds(Auth::id()))->count() < 2,
                    __('messages.forbidden')
                );
                [$type, $parentType] = array_map(
                    function ($id) { return $this->find($id)->dType->name; },
                    [$id, $parentId]
                );
                switch ($type) {
                    case '运营':
                        $movable = $parentType == '根';
                        break;
                    case '企业':
                        $movable = $parentType == '运营';
                        break;
                    case '学校':
                        $movable = $parentType != '企业' ? false
                            : $this->corpId($id) == $this->corpId($parentId);
                        break;
                    case '年级':
                        $movable = !in_array($parentType, ['学校', '其他']) ? false
                            : $this->corpId($id) == $this->corpId($parentId);
                        break;
                    case '班级':
                        $movable = !in_array($parentType, ['年级', '其他']) ? false
                            : $this->corpId($id) == $this->corpId($parentId);
                        break;
                    case '其他':
                        $movable = !in_array($parentType, ['运营', '企业'])
                            && $this->corpId($id) == $this->corpId($parentId);
                        break;
                    default:
                        $movable = false;
                        break;
                }
                throw_if(!$movable, new Exception(__('messages.forbidden')));
                $dept = $this->find($id);
                $dept->parent_id = $parentId === '#' ? null : intval($parentId);
                throw_if(!$dept->save(), new Exception(__('messages.fail')));
                # 更新部门对应企业/学校/年级/班级、菜单等对象
                switch ($dept->dType->name) {
                    case '企业':
                        $corp = Corp::whereDepartmentId($id)->first();
                        $company = Company::whereDepartmentId($parentId)->first();
                        $corp->update(['company_id' => $company->id]);
                        Menu::find($corp->menu_id)->first()->update([
                            'parent_id' => Menu::find($company->menu_id)->first()->id,
                        ]);
                        break;
                    case '年级':
                        $grade = Grade::whereDepartmentId($id)->first();
                        $parent = $this->find($parentId);
                        if ($parent->dType->name == '学校') {
                            $grade->update(['school_id' => $parent->school->id]);
                        }
                        break;
                    case '班级':
                        $class = Squad::whereDepartmentId($id)->first();
                        $parent = $this->find($parentId);
                        if ($parent->dType->name == '年级') {
                            $class->update(['grade_id' => $parent->grade->id]);
                        }
                        break;
                    default: # 学校
                        break;
                }
                !$dept->needSync($dept) ?: SyncDepartment::dispatch([$id], 'update', Auth::id());
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
    /**
     * 返回指定部门的所有上级（校级及以下）部门id
     *
     * @param integer $id
     * @return array
     */
    private function parentIds($id): array {
        
        static $ids = [];
        $dept = $this->find($id);
        $parent = $dept->parent;
        while ($parent->dType->name != '学校') {
            $ids[] = $parent->id;
            
            return $this->parentIds($parent->id);
        }
        $ids[] = $parent->id;
        
        return $ids;
        
    }
    
    /**
     * 根据根部门ID返回所有下级部门对象
     *
     * @param null $rootId
     * @return Collection|static[]
     * @throws Exception
     */
    private function nodes($rootId = null) {
        
        if (!isset($rootId)) {
            $nodes = $this->orderBy('order')->get();
        } else {
            $ids = collect([$rootId])->merge(
                $this->departmentIds(Auth::id())->intersect(
                    $this->subIds($rootId)
                )
            );
            $nodes = $this->orderBy('order')->whereIn('id', $ids)->get();
        }
        
        return $nodes;
        
    }
    
    /**
     * 返回用户所处的顶级部门id
     *
     * @return int
     */
    private function topId() {
        
        $levels = [];
        $ids = Auth::user()->deptIds();
        foreach ($ids as $id) {
            $level = 0;
            $levels[$id] = $this->level($id, $level);
        }
        asort($levels);
        
        return $this->find(array_key_first($levels))->parent_id;
        
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
    
    /**
     * 获取当前用户的根部门ID
     *
     * @param bool $subRoot
     * @return int|mixed
     */
    private function rootId($subRoot = false) {
        
        $user = Auth::user();
        $rootDepartmentTypeId = DepartmentType::whereName('根')->first()->id;
        $rootDId = Department::whereDepartmentTypeId($rootDepartmentTypeId)->first()->id;
        # 当前菜单id
        $menuId = session('menuId');
        $menu = Menu::find($menuId);
        # 学校的根菜单id
        $smId = $menu->menuId($menuId);
        # 学校的根部门id
        $sdId = $smId ? School::whereMenuId($smId)->first()->department_id : null;
        # 企业的根菜单id
        $cmId = $menu->menuId($menuId, '企业');
        # 企业的根部门id
        $cdId = $cmId ? Corp::whereMenuId($cmId)->first()->department_id : null;
        switch ($user->role()) {
            case '运营':
                return !$subRoot ? $rootDId : ($sdId ?? ($cdId ?? $rootDId));
            case '企业':
                return !$subRoot ? $cdId : ($sdId ?? $cdId);
            case '学校':
                return $sdId;
            default:
                return School::find($user->educator->school_id)->department_id;
        }
        
    }
    
}
