<?php

namespace App\Models;

use Eloquent;
use Exception;
use Carbon\Carbon;
use App\Helpers\Snippet;
use App\Helpers\ModelTrait;
use App\Events\CompanyCreated;
use App\Events\CompanyDeleted;
use App\Events\CompanyUpdated;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use App\Facades\DatatableFacade as Datatable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Facades\DB;

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

    protected $d, $m, $dt;
    
    function __construct(array $attributes = []) {
        
        parent::__construct($attributes);
        $this->d = app()->make('App\Models\Department');
        $this->m = app()->make('App\Models\Menu');
        $this->dt = app()->make('App\Models\DepartmentType');
    
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

    // /**
    //  * 保存运营者
    //  *
    //  * @param array $data
    //  * @param bool $fireEvent
    //  * @return bool
    //  */
    // function store(array $data, $fireEvent = false) {
    //
    //     try {
    //         DB::transaction(function () use ($data) {
    //             # 创建运营者
    //             $company = $this->create($data);
    //
    //             # 创建部门
    //             $department = $this->d->create([
    //                 'parent_id' => $this->d->where('parent_id', null)->first()->id,
    //                 'name' => $company->name,
    //                 'remark' => $company->remark,
    //                 'department_type_id' => $this->dt->where('name', '运营')->first()->id,
    //                 'enabled' => $company->enabled
    //             ]);
    //
    //             # 创建菜单
    //             $menu = $this->m->create([])
    //
    //         });
    //     } catch (Exception $e) {
    //         throw $e;
    //     }
    //     return true;
    //     if ($company && $fireEvent) {
    //         event(new CompanyCreated($company));
    //         return true;
    //     }
    //
    //     return $company ? true : false;
    //
    // }

    /**
     * 更新运营者
     *
     * @param array $data
     * @param $id
     * @param bool $fireEvent
     * @return bool
     */
    function modify(array $data, $id, $fireEvent = false) {

        $company = self::find($id);
        $updated = $company->update($data);
        if ($updated && $fireEvent) {
            event(new CompanyUpdated($company));
            return true;
        }

        return $updated ? true : false;

    }

    /**
     * 删除运营者
     *
     * @param $id
     * @param bool $fireEvent
     * @return bool
     * @throws Exception
     */
    function remove($id, $fireEvent = false) {

        $company = $this->find($id);
        if (!$company) { return false; }
        $removed = self::removable($company) ? $company->delete() : false;
        if ($removed && $fireEvent) {
            event(new CompanyDeleted($company));
            return true;
        }

        return $removed ?? false;

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
