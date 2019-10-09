<?php
namespace App\Models;

use App\Facades\Datatable;
use App\Helpers\{ModelTrait};
use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\{Builder,
    Collection,
    Model,
    Relations\BelongsTo,
    Relations\HasMany,
    Relations\HasManyThrough};
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * App\Models\Company 运营者公司
 *
 * @property int $id
 * @property int $department_id
 * @property int $menu_id
 * @property string $name 运营者公司名称
 * @property string $remark 运营者公司备注
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $enabled
 * @property-read Collection|Corp[] $corps
 * @property-read int|null $corps_count
 * @property-read Department $department
 * @property-read Menu $menu
 * @property-read Collection|School[] $schools
 * @property-read int|null $schools_count
 * @method static Builder|Company newModelQuery()
 * @method static Builder|Company newQuery()
 * @method static Builder|Company query()
 * @method static Builder|Company whereCreatedAt($value)
 * @method static Builder|Company whereDepartmentId($value)
 * @method static Builder|Company whereEnabled($value)
 * @method static Builder|Company whereId($value)
 * @method static Builder|Company whereMenuId($value)
 * @method static Builder|Company whereName($value)
 * @method static Builder|Company whereRemark($value)
 * @method static Builder|Company whereUpdatedAt($value)
 * @mixin Eloquent
 * @property-read Department $dept
 */
class Company extends Model {
    
    use ModelTrait;
    
    protected $fillable = [
        'department_id', 'menu_id', 'name', 'remark', 'enabled',
    ];
    
    /** @return BelongsTo */
    function dept() { return $this->belongsTo('App\Models\Department', 'department_id'); }
    
    /** @return BelongsTo */
    function menu() { return $this->belongsTo('App\Models\Menu'); }
    
    /** @return HasMany */
    function corps() { return $this->hasMany('App\Models\Corp'); }
    
    /** @return HasManyThrough */
    function schools() { return $this->hasManyThrough('App\Models\School', 'App\Models\Corp'); }
    
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
                    return $this->iconHtml($d, 'company');
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
        
        return Datatable::simple($this, $columns);
        
    }
    
    /**
     * 保存运营者
     *
     * @param array $data
     * @return bool
     * @throws Throwable
     */
    function store(array $data) {
        
        try {
            DB::transaction(function () use ($data) {
                # 创建运营者、对应部门及菜单
                $company = $this->create($data);
                $department = (new Department)->stow($company);
                $menu = (new Menu)->stow($company);
                # 更新“运营者”的部门id和菜单id
                $company->update([
                    'department_id' => $department->id,
                    'menu_id'       => $menu->id,
                ]);
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
    /**
     * 更新运营者
     *
     * @param array $data
     * @param $id
     * @return bool
     * @throws Throwable
     */
    function modify(array $data, $id) {
        
        return $this->revise(
            $this, $data, $id,
            function ($company) {
                (new Department)->alter($company);
                (new Menu)->alter($company);
            }
        );
        // try {
        //     DB::transaction(function () use ($data, $id) {
        //         throw_if(
        //             !$company = $this->find($id),
        //             new Exception(__('messages.not_found'))
        //         );
        //         $company->update($data);
        //         (new Department)->alter($company);
        //         (new Menu)->alter($company);
        //     });
        // } catch (Exception $e) {
        //     throw $e;
        // }
        //
        // return true;
        
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
        
        try {
            DB::transaction(function () use ($id) {
                $this->mdPurge($id, ['purge.company_id' => ['Corp']]);
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
}
