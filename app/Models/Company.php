<?php
namespace App\Models;

use App\Facades\Datatable;
use App\Helpers\ModelTrait;
use App\Helpers\Snippet;
use App\Http\Requests\CompanyRequest;
use Carbon\Carbon;
use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Facades\DB;
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
     * 运营者列表
     *
     * @return array
     */
    function index() {
        
        $columns = [
            ['db' => 'Company.id', 'dt' => 0],
            [
                'db'        => 'Company.name', 'dt' => 1,
                'formatter' => function ($d) {
                    return sprintf(Snippet::ICON, 'fa-building text-blue', '') .
                        '<span class="text-blue">' . $d . '</span>';
                },
            ],
            ['db' => 'Company.remark', 'dt' => 2],
            ['db' => 'Company.created_at', 'dt' => 3],
            ['db' => 'Company.updated_at', 'dt' => 4],
            [
                'db'        => 'Company.enabled', 'dt' => 5,
                'formatter' => function ($d, $row) {
                    return Datatable::status($d, $row, false);
                },
            ],
        ];
        
        return Datatable::simple($this->getModel(), $columns);
        
    }
    
    /**
     * 保存运营者
     *
     * @param CompanyRequest $request
     * @return mixed|bool|null
     * @throws Throwable
     */
    function store(CompanyRequest $request) {
        
        $company = null;
        try {
            DB::transaction(function () use ($request, &$company) {
                # 创建运营者、对应部门及菜单
                $company = $this->create($request->all());
                $department = (new Department)->storeDepartment($company);
                $menu = (new Menu)->storeMenu($company);
                # 更新“运营者”的部门id和菜单id
                $company->update([
                    'department_id' => $department->id,
                    'menu_id'       => $menu->id,
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
     * @throws Throwable
     */
    function modify(CompanyRequest $request, $id) {
        
        $company = null;
        try {
            DB::transaction(function () use ($request, $id, &$company) {
                $company = $this->find($id);
                $company->update($request->all());
                (new Department)->modifyDepartment($company);
                (new Menu)->modifyMenu($company);
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
     * @throws Exception
     * @throws Throwable
     */
    function remove($id = null) {
        
        return $this->del($this, $id);
        
    }
    
    /**
     * 删除指定运营者的所有数据
     *
     * @param $id
     * @return bool
     * @throws Throwable
     */
    function purge($id) {
        
        try {
            DB::transaction(function () use ($id) {
                $company = $this->find($id);
                $this->delRelated('company_id', 'Corp', $id);
                (new Department)->remove($company->department_id);
                (new Menu)->remove($company->menu_id);
                $company->delete();
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
}
