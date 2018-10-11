<?php
namespace App\Models;

use App\Helpers\Constant;
use App\Helpers\HttpStatusCode;
use App\Helpers\ModelTrait;
use App\Helpers\Snippet;
use App\Jobs\SyncDepartment;
use Carbon\Carbon;
use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
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
 * @property-read DepartmentType $departmentType
 * @property-read Grade $grade
 * @property-read Department|null $parent
 * @property-read School $school
 * @property-read Squad $squad
 * @property-read Collection|User[] $users
 * @property-read Collection|Tag[] $tags
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
 * @mixin Eloquent
 */
class Department extends Model {
    
    use ModelTrait;
    
    protected $fillable = [
        'id', 'parent_id', 'department_type_id', 'name',
        'remark', 'order', 'enabled', 'synced',
    ];
    
    /**
     * 部门类型
     *
     * @return BelongsTo
     */
    function departmentType() { return $this->belongsTo('App\Models\DepartmentType'); }
    
    /**
     * 运营者
     *
     * @return HasOne
     */
    function company() { return $this->hasOne('App\Models\Company'); }
    
    /**
     * 企业
     *
     * @return HasOne
     */
    function corp() { return $this->hasOne('App\Models\Corp'); }
    
    /**
     * 学校
     *
     * @return HasOne
     */
    function school() { return $this->hasOne('App\Models\School'); }
    
    /**
     * 年级
     *
     * @return HasOne
     */
    function grade() { return $this->hasOne('App\Models\Grade'); }
    
    /**
     * 班级
     *
     * @return HasOne
     */
    function squad() { return $this->hasOne('App\Models\Squad'); }
    
    /**
     * 用户
     *
     * @return BelongsToMany
     */
    function users() { return $this->belongsToMany('App\Models\User', 'departments_users'); }
    
    /**
     * 返回部门所属的标签
     *
     * @return BelongsToMany
     */
    function tags() { return $this->belongsToMany('App\Models\Tag', 'departments_tags'); }
    
    /**
     * 直接上级部门
     *
     * @return BelongsTo
     */
    function parent() {
        
        return $this->belongsTo('App\Models\Department', 'parent_id');
        
    }
    
    /**
     * 创建并返回指定（运营/企业/学校/年级/班级）对应的部门对象
     *
     * @param Model $model
     * @param null $belongsTo
     * @return Department|bool|Model
     * @throws Throwable
     */
    function storeDepartment(Model $model, $belongsTo = null) {
        
        $department = null;
        try {
            DB::transaction(function () use ($model, $belongsTo, &$department) {
                list($dtType, $dtId) = (new DepartmentType)->dtId($model);
                $department = $this->store([
                    'parent_id'          => $belongsTo
                        ? $model->{$belongsTo}->department_id
                        : $this::whereParentId(null)->first()->id,
                    'name'               => $model->{'name'},
                    'remark'             => $model->{'remark'},
                    'department_type_id' => $dtId,
                    'order'              => $this->all()->max('order') + 1,
                    'enabled'            => $model->{'enabled'},
                ]);
                # 创建年级/班级主任用户与部门的绑定关系
                $this->updateDu($dtType, $model, $department);
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return $department;
        
    }
    
    /**
     * 创建部门
     *
     * @param array $data
     * @return Department|bool|Model
     */
    function store(array $data) {
        
        $department = $this->create($data);
        if ($department && $this->needSync($department)) {
            $this->sync($department->id, 'create');
        }
        
        return $department;
        
    }
    
    /**
     * 判断指定部门是否需要同步到企业微信
     *
     * @param Department $department
     * @return bool
     */
    private function needSync(Department $department) {
        
        return !in_array(
            $department->departmentType->name, ['根', '运营', '企业']
        );
        
    }
    
    /**
     * 同步企业微信部门
     *
     * @param integer $id
     * @param string $action
     * @return bool
     */
    private function sync($id, $action) {
        
        $department = $this->find($id);
        $data = [
            'id' => $id,
            'schoolIds' => [$this->schoolId()],
            'corp_id' => $this->corpId($id)
        ];
        if ($action != 'delete') {
            $data = array_merge(
                $data,
                [
                    'name'     => $department->name,
                    'parentid' => $department->departmentType->name == '学校'
                        ? $department->school->corp->departmentid
                        : $department->parent_id,
                    'order'    => $department->order,
                ]
            );
        }
        SyncDepartment::dispatch($data, Auth::id(), $action);
        
        return true;
        
    }
    
    /**
     * 返回指定部门所属的企业id
     *
     * @param $id
     * @return int|mixed
     */
    function corpId($id) {
        
        $department = $this->find($id);
        switch ($department->departmentType->name) {
            case '运营':
                return null;
            case '企业':
                return Corp::whereDepartmentId($id)->first()->id;
            default:
                $parent = $this->find($id)->parent;
                while ($parent->departmentType->name != '企业') {
                    $id = $parent->id;
                    
                    return $this->corpId($id);
                }
                
                return Corp::whereDepartmentId($parent->id)->first()->id;
        }
        
    }
    
    /**
     * 更新年级/班级主任与部门的绑定关系
     *
     * @param $dtType
     * @param $model
     * @param $department
     * @throws Throwable
     */
    private function updateDu($dtType, $model, $department): void {
        
        if (in_array($dtType, ['grade', 'squad'])) {
            $users = User::with('educators')
                ->whereIn('educator.id', explode(',', $model->{'educator_ids'}))
                ->get();
            if (!empty($users)) {
                (new DepartmentUser())->storeByDepartmentId(
                    $department->id, $users->pluck('user.id')
                );
            }
        }
        
    }
    
    /**
     * 更新（运营/企业/学校/年级/班级）对应的部门
     *
     * @param Model $model
     * @param null $beLongsTo
     * @return void
     * @throws Throwable
     */
    function modifyDepartment(Model $model, $beLongsTo = null) {
        
        try {
            DB::transaction(function () use ($model, $beLongsTo) {
                list($dtType, $dtId) = (new DepartmentType)->dtId($model);
                $data = [
                    'name'               => $model->{'name'},
                    'remark'             => $model->{'remark'},
                    'department_type_id' => $dtId,
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
                $department = $this->modify($data, $model->{'department_id'});
                $this->updateDu($dtType, $model, $department);
            });
        } catch (Exception $e) {
            throw $e;
        }
        
    }
    
    /**
     * 更新部门
     *
     * @param array $data
     * @param $id
     * @return bool|Collection|Model|null|static|static[]
     */
    function modify(array $data, $id) {
        
        $department = self::find($id);
        $updated = $department->update($data);
        if ($this->needSync($department) && $updated) {
            $this->sync($id, 'update');
        }
        
        return $updated ?? $this->find($id);
        
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
        
        $department = $this->find($id);
        try {
            DB::transaction(function () use ($id, $department) {
                $du = new DepartmentUser;
                $user = Auth::user();
                $ids = array_merge([$id], $this->subDepartmentIds($id));
                $userIds = array_unique(
                    $du->whereIn('department_id', $ids)->pluck('user_id')->toArray()
                );
                # 删除部门&用户绑定关系
                (new DepartmentUser)->whereIn('department_id', $ids)->delete();
                # 删除部门&标签绑定关系
                (new DepartmentTag)->whereIn('department_id', $ids)->delete();
                # 更新被删除部门所包含用户对应的企业微信会员信息
                array_map(
                    function ($userId) use ($user) {
                        $action = User::find($userId)->departments->count() ? 'update' : 'delete';
                        $user->sync($userId, $action);
                    }, $userIds
                );
                # 删除指定企业微信部门及其子部门
                $syncIds = [];
                foreach ($ids as $id) {
                    if ($this->needSync($this->find($id))) {
                        $level = 0;
                        $syncIds[$id] = $this->level($id, $level);
                    }
                }
                arsort($syncIds);
                array_map(
                    function ($id) { $this->sync($id, 'delete'); },
                    array_keys($syncIds)
                );
                # 删除指定部门及其子部门
                $this->whereIn('id', $ids)->delete();
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
    /**
     * 返回指定部门所有子部门的id
     *
     * @param $id
     * @return array
     */
    function subDepartmentIds($id) {
        
        static $subDepartmentIds;
        $childrenIds = Department::whereParentId($id)->pluck('id')->toArray();
        if ($childrenIds) {
            foreach ($childrenIds as $childId) {
                $subDepartmentIds[] = $childId;
                $this->subDepartmentIds($childId);
            }
        }
        
        return $subDepartmentIds ?? [];
        
    }
    
    /**
     * 返回指定部门所处的级别
     *
     * @param integer $id 部门ID
     * @param integer $level 部门所处级别
     * @return int|null
     */
    private function level($id, &$level) {
        
        $department = $this->find($id);
        if (!$department) {
            return null;
        }
        $parent = $department->parent;
        if ($parent) {
            $level += 1;
            $this->level($parent->id, $level);
        }
        
        return $level;
        
    }
    
    /**
     * 部门列表/排序/移动
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    function index() {
        
        $response = response()->json();
        switch (Request::input('action')) {
            case 'tree':
                $response = response()->json(
                    $this->tree($this->rootDepartmentId(true))
                );
                break;
            case 'sort':
                # 保存部门排序
                $orders = Request::get('data');
                $originalOrders = $this->orderBy('order')
                    ->whereIn('id', array_keys($orders))
                    ->get()->pluck('order', 'id')->toArray();
                foreach ($orders as $id => $order) {
                    $originalOrder = array_slice($originalOrders, $order, 1, true);
                    $this->find($id)->update([
                        'order' => $originalOrder[key($originalOrder)],
                    ]);
                };
                break;
            case 'move':
                # 移动部门
                $id = Request::input('id');
                $parentId = Request::input('parentId');
                $department = $this->find($id);
                $parentDepartment = $this->find($parentId);
                abort_if(
                    !$department || !$parentDepartment,
                    HttpStatusCode::NOT_FOUND
                );
                if ($department->movable($id, $parentId)) {
                    $moved = $department->move($id, $parentId);
                    if ($moved && $this->needSync($department)) {
                        $this->sync($id, 'update');
                    }
                }
                break;
            default:
                break;
        }
        
        return $response;
    
    }
    
    /** Helper functions -------------------------------------------------------------------------------------------- */

    /**
     * 获取用于显示jstree的部门数据
     *
     * @param null $rootId
     * @return array
     */
    function tree($rootId = null) {
        
        $user = Auth::user();
        $isSuperRole = in_array($user->group->name, Constant::SUPER_ROLES);
        if (isset($rootId)) {
            $departments = $this->nodes($rootId);
        } else {
            $rootId = $isSuperRole
                ? $this->rootDepartmentId(true)
                : $this->topDeptId();
            $departments = $this->nodes($rootId);
        }
        $allowedDepartmentIds = $this->departmentIds($user->id);
        $nodes = [];
        for ($i = 0; $i < sizeof($departments); $i++) {
            $id = $departments[$i]['id'];
            $parentId = $i == 0 ? '#' : $departments[$i]['parent_id'];
            $departmentTypeId = $departments[$i]['department_type_id'];
            $departmentType = DepartmentType::find($departmentTypeId)->name;
            $name = $departments[$i]['name'];
            $enabled = $departments[$i]['enabled'];
            $color = Constant::NODE_TYPES[$departmentType]['color'];
            $type = Constant::DEPARTMENT_TYPES[$departmentType];
            $title = '';
            $syncMark = '';
            if (!in_array($type, ['root', 'company', 'corp'])) {
                $synced = $departments[$i]['synced'];
                $title = $synced ? '已同步' : '未同步';
                $syncMark = $synced
                    ? ' <span class="text-green">*</span>'
                    : ' <span class="text-red">*</span>';
            }
            $text = sprintf(
                Snippet::NODE_TEXT,
                $enabled ? $color : 'text-gray',
                $title, $name, $syncMark
            );
            $selectable = $isSuperRole ? 1 : (in_array($id, $allowedDepartmentIds) ? 1 : 0);
            $corp_id = !in_array($type, ['root', 'company']) ? $this->corpId($id) : null;
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
     * 根据根部门ID返回所有下级部门对象
     *
     * @param null $rootId
     * @return Collection|static[]
     */
    private function nodes($rootId = null) {
        
        if (!isset($rootId)) {
            $nodes = $this->orderBy('order')->all();
        } else {
            $departmentIds = array_merge(
                [$rootId],
                array_intersect(
                    $this->departmentIds(Auth::id()),
                    $this->subDepartmentIds($rootId)
                )
            );
            $nodes = $this->orderBy('order')->whereIn('id', $departmentIds)->get();
        }
        
        return $nodes;
        
    }
    
    /**
     * 获取当前用户的根部门ID
     *
     * @param bool $subRoot
     * @return int|mixed
     */
    private function rootDepartmentId($subRoot = false) {
        
        $user = Auth::user();
        $role = $user->group->name;
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
        switch ($role) {
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
    
    /**
     * 返回用户所处的顶级部门id
     *
     * @return int
     */
    private function topDeptId() {
        
        $user = Auth::user();
        $ids = $user->departments->pluck('id')->toArray();
        $levels = [];
        foreach ($ids as $id) {
            $level = 0;
            $levels[$id] = self::level($id, $level);
        }
        asort($levels);
        reset($levels);
        $topLevelId = key($levels);
        
        return self::find($topLevelId)->parent->id;
        
    }
    
    /**
     * 判断指定的节点能否移至指定的节点下
     *
     * @param $id
     * @param $parentId
     * @return bool
     */
    private function movable($id, $parentId) {
        
        if (!isset($id, $parentId)) {
            return false;
        }
        $allowedDepartmentIds = $this->departmentIds(Auth::id());
        # 如果部门(被移动的部门和目标部门）不在当前用户的可见范围内，则抛出401异常
        abort_if(
            !in_array($id, $allowedDepartmentIds) ||
            !in_array($parentId, $allowedDepartmentIds),
            HttpStatusCode::UNAUTHORIZED,
            __('messages.forbidden')
        );
        $department = $this->find($id);
        $parentDepartment = $this->find($parentId);
        $type = $department->departmentType->name;
        $parentType = $parentDepartment->departmentType->name;
        switch ($type) {
            case '运营':
                return $parentType == '根';
            case '企业':
                return $parentType == '运营';
            case '学校':
                return $parentType == '企业'
                    ? $this->corpId($id) == $this->corpId($parentId)
                    : false;
            case '年级':
                return in_array($parentType, ['学校', '其他'])
                    ? $this->corpId($id) == $this->corpId($parentId)
                    : false;
            case '班级':
                return in_array($parentType, ['年级', '其他'])
                    ? $this->corpId($id) == $this->corpId($parentId)
                    : false;
            case '其他':
                return !in_array($parentType, ['运营', '企业'])
                    && $this->corpId($id) == $this->corpId($parentId);
            default:
                return false;
        }
        
    }
    
    /**
     * 更改部门所处位置
     *
     * @param $id
     * @param $parentId
     * @return bool
     * @throws Throwable
     */
    private function move($id, $parentId) {
        
        $moved = false;
        try {
            DB::transaction(function () use ($id, $parentId, &$moved) {
                $deparment = $this->find($id);
                if (!isset($deparment)) {
                    $moved = false;
                } else {
                    $deparment->parent_id = $parentId === '#' ? null : intval($parentId);
                    $moved = $deparment->save();
                    # 更新部门对应企业/学校/年级/班级、菜单等对象
                    if ($moved) {
                        switch ($deparment->departmentType->name) {
                            case '企业':
                                $corp = Corp::whereDepartmentId($id)->first();
                                $company = Company::whereDepartmentId($parentId)->first();
                                $corp->update(['company_id' => $company->id]);
                                Menu::find($corp->menu_id)->first()->update([
                                    'parent_id' => Menu::find($company->menu_id)->first()->id,
                                ]);
                                break;
                            case '学校':
                                break;
                            case '年级':
                                $grade = Grade::whereDepartmentId($id)->first();
                                $parent = $this->find($parentId);
                                if ($parent->departmentType->name == '学校') {
                                    $grade->update(['school_id' => $parent->school->id]);
                                }
                                break;
                            case '班级':
                                $class = Squad::whereDepartmentId($id)->first();
                                $parent = $this->find($parentId);
                                if ($parent->departmentType->name == '年级') {
                                    $class->update(['grade_id' => $parent->grade->id]);
                                }
                                break;
                            default:
                                break;
                        }
                    }
                }
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return $moved;
        
    }
    
    /**
     * 返回所有叶节点部门
     *
     * @return array
     */
    function leaves() {
        
        $leaves = [];
        $leafPath = [];
        $departments = $this->nodes();
        /** @var Department $department */
        foreach ($departments as $department) {
            if (empty($department->children()->count())) {
                $path = self::leafPath($department->id, $leafPath);
                $leaves[$department->id] = $path;
                $leafPath = [];
            }
        }
        
        return $leaves;
        
    }
    
    /**
     * 直接子部门
     *
     * @return HasMany
     */
    function children() {
        
        return $this->hasMany('App\Models\Department', 'parent_id', 'id');
        
    }
    
    /**
     * 获取指定部门的完整路径
     *
     * @param $id
     * @param array $path
     * @return string
     */
    public function leafPath($id, array &$path) {
        
        $department = self::find($id);
        if (!isset($department)) {
            return '';
        }
        $path[] = $department->name;
        if (isset($department->parent_id)) {
            self::leafPath($department->parent_id, $path);
        }
        krsort($path);
        
        return implode(' . ', $path);
        
    }
    
    /**
     * 获取联系人树
     *
     * @param bool $contact - 部门树是否包含部门中的联系人
     * @return array|JsonResponse
     */
    function contacts($contact = true) {
        
        $user = Auth::user();
        $contacts = [];
        if (in_array($user->group->name, Constant::SUPER_ROLES)) {
            $departmentId = School::find($this->schoolId())->department_id;
            $visibleNodes = $this->tree($departmentId);
        } else {
            $nodes = $this->tree();
            # 当前用户可访问的所有部门id
            $allowedDepartmentIds = $this->departmentIds($user->id);
            # 当前用户可访问部门的所有上级部门id
            $allowedParentIds = [];
            foreach ($allowedDepartmentIds as $id) {
                $allowedParentIds[$id] = $this->parentIds($id);
            }
            # 对当前用户可见的所有部门节点
            $visibleNodes = [];
            foreach ($nodes as $node) {
                if (!$node['selectable']) {
                    foreach ($allowedParentIds as $departmentId => $parentIds) {
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
            # 获取可见部门下的所有联系人（学生、教职员工）
            foreach ($visibleNodes as $node) {
                if ($node['selectable']) {
                    $this->find($node['id'])->users->each(
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
     * 返回指定部门的所有上级（校级及以下）部门id
     *
     * @param integer $id
     * @return array
     */
    private function parentIds($id): array {
        
        static $ids = [];
        $d = $this->find($id);
        $p = $d->parent;
        while ($p->departmentType->name != '学校') {
            $ids[] = $p->id;
            
            return $this->parentIds($p->id);
        }
        $ids[] = $p->id;
        
        return $ids;
        
    }
    
    /**
     * 获取指定部门（含所有子部门）的所有用户
     *
     * @param array $ids
     * @return array
     */
    function partyUsers($ids) {
        
        $departmentIds = [];
        foreach ($ids as $id) {
            $departmentIds = array_merge(
                $departmentIds, $this->subDepartmentIds($id)
            );
        }
        $ids = array_unique($departmentIds);
        $userIds = [];
        foreach ($ids as $id) {
            $userIds = array_merge(
                $userIds, $this->find($id)->users->pluck('id')->toArray()
            );
        }
        $userIds = array_unique($userIds);
        
        return User::whereIn('id', $userIds)->get();
        
    }
    
    /**
     * 根据部门ID返回其父级部门中类型为$type的部门ID
     *
     * @param $id
     * @param string $type
     * @return int|mixed
     */
    function departmentId($id, $type = '学校') {
        
        $department = $this->find($id);
        if (!$department) {
            return null;
        }
        $departmentType = $department->departmentType->name;
        while ($departmentType != $type) {
            $department = $department->parent;
            if (!$department) {
                return null;
            }
            $departmentType = $department->departmentType->name;
        }
        
        return $department->id;
        
    }
    
}
