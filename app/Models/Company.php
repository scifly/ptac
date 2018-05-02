<?php

namespace App\Models;

use App\Http\Requests\CompanyRequest;
use Eloquent;
use Exception;
use Carbon\Carbon;
use App\Helpers\Snippet;
use App\Helpers\ModelTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use App\Facades\DatatableFacade as Datatable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Facades\DB;
use ReflectionException;
use Throwable;

/**
 * App\Models\Company 运营者公司
 *
 * @property int $id
 * @property string $name 运营者公司名称
 * @property string $remark 运营者公司备注
 * @property string $corpid 与运营者公司对应的企业号id
 * @property int $menu_id 对应的菜单ID
 * @property int $department_id 对应的部门ID
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $enabled
 * @method static Builder|Company whereCorpid($value)
 * @method static Builder|Company whereId($value)
 * @method static Builder|Company whereName($value)
 * @method static Builder|Company whereRemark($value)
 * @method static Builder|Company whereCreatedAt($value)
 * @method static Builder|Company whereUpdatedAt($value)
 * @method static Builder|Company whereDepartmentId($value)
 * @method static Builder|Company whereMenuId($value)
 * @method static Builder|Company whereEnabled($value)
 * @mixin Eloquent
 * @property-read Collection|Company[] $corps
 * @property-read Collection|Operator[] $operators
 * @property-read Collection|School[] $schools
 * @property-read Department $department
 * @property-read Menu $menu
 */
class Company extends Model {

    use ModelTrait;

    protected $fillable = [
        'name', 'remark', 'department_id',
        'menu_id', 'enabled',
    ];

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
     * 获取指定运营者公司下属的企业对象
     *
     * @return HasMany
     */
    function corps() { return $this->hasMany('App\Models\Corp'); }

    /**
     * 通过Corp中间对象获取所有的学校对象
     *
     * @return HasManyThrough
     */
    function schools() {

        return $this->hasManyThrough('App\Models\School', 'App\Models\Corp');

    }
    
    /**
     * 保存运营者
     *
     * @param CompanyRequest $request
     * @return mixed|bool|null
     * @throws Exception
     */
    function store(CompanyRequest $request) {
    
        $company = null;
        try {
            DB::transaction(function () use ($request, &$company) {
                # 创建运营者、对应部门及菜单
                $company = $this->create($request->all());
                $department = (new Department())->storeDepartment($company);
                $menu = (new Menu())->storeMenu($company);
                # 更新“运营者”的部门id和菜单id
                $company->update([
                    'department_id' => $department->id,
                    'menu_id' => $menu->id
                ]);
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return $company;
    
    }
    
    /**
     * 更新运营者
     *
     * @param CompanyRequest $request
     * @param $id
     * @return mixed|bool|null
     * @throws Exception
     */
    function modify(CompanyRequest $request, $id) {

        $company = null;
        try {
            DB::transaction(function () use ($request, $id, &$company) {
                $company = $this->find($id);
                $company->update($request->all());
                (new Department())->modifyDepartment($company);
                (new Menu())->modifyMenu($company);
            });
        } catch (Exception $e) {
            throw $e;
        }

        return $company ? $this->find($id) : null;
        
    }
    
    /**
     * 删除运营者
     *
     * @param $id
     * @return bool
     * @throws ReflectionException
     * @throws Exception
     * @throws Throwable
     */
    function remove($id) {
        
        $company = $this->find($id);
        if ($this->removable($company)) {
            $department = $company->department;
            $menu = $company->menu;
            return $company->delete()
                && (new Department())->removeDepartment($department)
                && (new Menu())->removeMenu($menu);
        }
        
        return false;
        
    }

    /**
     * 运营者列表
     *
     * @return array
     */
    function datatable() {

        $columns = [
            ['db' => 'Company.id', 'dt' => 0],
            [
                'db' => 'Company.name', 'dt' => 1,
                'formatter' => function ($d) {
                    return sprintf(Snippet::ICON, 'fa-building text-blue', '') .
                        '<span class="text-blue">' . $d . '</span>';
                }
            ],
            ['db' => 'Company.remark', 'dt' => 2],
            ['db' => 'Company.created_at', 'dt' => 3],
            ['db' => 'Company.updated_at', 'dt' => 4],
            [
                'db' => 'Company.enabled', 'dt' => 5,
                'formatter' => function ($d, $row) {
                    return Datatable::dtOps($d, $row, false);
                }
            ],
        ];

        return Datatable::simple($this->getModel(), $columns);

    }

}
