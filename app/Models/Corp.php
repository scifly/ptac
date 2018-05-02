<?php

namespace App\Models;

use App\Facades\DatatableFacade as Datatable;
use App\Helpers\ModelTrait;
use App\Helpers\Snippet;
use App\Http\Requests\CorpRequest;
use Carbon\Carbon;
use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use ReflectionException;
use Throwable;

/**
 * App\Models\Corp 企业
 *
 * @property int $id
 * @property string $name 企业名称
 * @property string $acronym 企业名称缩写（首字母缩略词）
 * @property string $corpid 企业号id
 * @property string $contact_sync_secret "通讯录同步"应用Secret
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $enabled
 * @property int $menu_id 对应的菜单ID
 * @property int $company_id 所属运营者公司ID
 * @property int $department_id 对应的部门ID
 * @method static Builder|Corp whereCorpid($value)
 * @method static Builder|Corp whereContactSyncSecret($value)
 * @method static Builder|Corp whereCreatedAt($value)
 * @method static Builder|Corp whereEnabled($value)
 * @method static Builder|Corp whereId($value)
 * @method static Builder|Corp whereName($value)
 * @method static Builder|Corp whereAcronym($value)
 * @method static Builder|Corp whereUpdatedAt($value)
 * @method static Builder|Corp whereCompanyId($value)
 * @method static Builder|Corp whereDepartmentId($value)
 * @method static Builder|Corp whereMenuId($value)
 * @mixin Eloquent
 * @property-read Company $company
 * @property-read Collection|Department[] $departments
 * @property-read Collection|Grade[] $grades
 * @property-read Collection|School[] $schools
 * @property-read Collection|Team[] $teams
 * @property-read Department $department
 * @property-read Menu $menu
 */
class Corp extends Model {
    
    use ModelTrait;
    
    protected $fillable = [
        'name', 'acronym', 'company_id',
        'corpid', 'contact_sync_secret',
        'menu_id', 'department_id', 'enabled',
    ];
    
    protected $d, $m;
    
    function __construct(array $attributes = []) {
        
        parent::__construct($attributes);
        $this->d = app()->make('App\Models\Department');
        $this->m = app()->make('App\Models\Menu');
        
    }
    
    /**
     * 返回对应的部门对象
     *
     * @return BelongsTo
     */
    function department() { return $this->belongsTo('App\Models\Department'); }
    
    /**
     * 返回对应的菜单对象
     *
     * @return BelongsTo
     */
    function menu() { return $this->belongsTo('App\Models\Menu'); }
    
    /**
     * 获取所属运营者公司对象
     *
     * @return BelongsTo
     */
    function company() { return $this->belongsTo('App\Models\Company'); }
    
    /**
     * 获取下属学校对象
     *
     * @return HasMany
     */
    function schools() { return $this->hasMany('App\Models\School'); }
    
    /**
     * 通过School中间对象获取所有年级对象
     *
     * @return HasManyThrough
     */
    function grades() {
        
        return $this->hasManyThrough('App\Models\Grade', 'App\Models\School');
        
    }
    
    /**
     * 通过School中间对象获取所有教职员工组对象
     *
     * @return HasManyThrough
     */
    function teams() {
        
        return $this->hasManyThrough('App\Models\Team', 'App\Models\School');
        
    }
    
    /**
     * 保存企业
     *
     * @param CorpRequest $request
     * @return mixed|bool|null
     * @throws Exception
     */
    function store(CorpRequest $request) {
        
        $corp = null;
        try {
            DB::transaction(function () use ($request, &$corp) {
                # 创建企业微信、对应部门及菜单
                $corp = $this->create($request->all());
                $department = $this->d->storeDepartment(
                    $corp, 'company'
                );
                $menu = $this->m->storeMenu(
                    $corp, 'company'
                );
                # 更新“企业微信”的部门id和菜单id
                $corp->update([
                    'department_id' => $department->id,
                    'menu_id' => $menu->id
                ]);
            });
        } catch (Exception $e) {
            throw $e;
        };
        
        return $corp;
        
    }
    
    /**
     * 更新企业
     *
     * @param CorpRequest $request
     * @param $id
     * @return mixed|bool|null
     * @throws Exception
     */
    function modify(CorpRequest $request, $id) {
        
        $corp = null;
        try {
            DB::transaction(function () use ($request, $id, &$corp) {
                $corp = $this->find($id);
                $corp->update($request->all());
                $this->d->modifyDepartment($corp, 'company');
                $this->m->modifyMenu($corp, 'company');
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return $corp ? $this->find($id) : null;
        
    }
    
    /**
     * 删除企业
     *
     * @param $id
     * @return bool
     * @throws ReflectionException
     * @throws Throwable
     */
    function remove($id) {
        
        $corp = $this->find($id);
        if ($this->removable($corp)) {
            $department = $corp->department;
            $menu = $corp->menu;
            return $corp->delete()
                && $this->d->removeDepartment($department)
                && $this->m->removeMenu($menu);
        }
        
        return false;
        
    }
    
    /**
     * 根据角色 & 菜单id获取corp_id
     *
     * @return int|mixed
     */
    function corpId() {
        
        if (!Session::exists('menuId')) { return null; }
        $user = Auth::user();
        switch ($user->group->name) {
            case '运营':
            case '企业':
                $corpMenuId = $this->menu->menuId(session('menuId'), '企业');
                return $corpMenuId ? $this->whereMenuId($corpMenuId)->first()->id : null;
            case '学校':
                $schoolMenuId = $this->menu->menuId(session('menuId'));
                return School::whereMenuId($schoolMenuId)->first()->corp_id;
            default:
                return School::find($user->educator->school_id)->corp_id;
        }
        
    }
    
    
    
    /**
     * 企业列表
     *
     * @return mixed
     */
    function datatable() {
        
        $columns = [
            ['db' => 'Corp.id', 'dt' => 0],
            [
                'db' => 'Corp.name', 'dt' => 1,
                'formatter' => function ($d) {
                    return sprintf(Snippet::ICON, 'fa-weixin text-green', '') .
                        '<span class="text-green">' . $d . '</span>';
                }
            ],
            ['db' => 'Corp.acronym', 'dt' => 2],
            [
                'db' => 'Company.name as companyname', 'dt' => 3,
                'formatter' => function ($d) {
                    return sprintf(Snippet::ICON, 'fa-building text-blue', '') .
                        '<span class="text-blue">' . $d . '</span>';
                }
            ],
            ['db' => 'Corp.corpid', 'dt' => 4],
            ['db' => 'Corp.contact_sync_secret', 'dt' => 5],
            ['db' => 'Corp.created_at', 'dt' => 6],
            ['db' => 'Corp.updated_at', 'dt' => 7],
            [
                'db' => 'Corp.enabled', 'dt' => 8,
                'formatter' => function ($d, $row) {
                    return Datatable::dtOps($d, $row, false);
                },
            ],
        ];
        $joins = [
            [
                'table' => 'companies',
                'alias' => 'Company',
                'type' => 'INNER',
                'conditions' => [
                    'Company.id = Corp.company_id',
                ],
            ],
        ];
        
        return Datatable::simple(
            $this->getModel(), $columns, $joins
        );
        
    }
    
}
