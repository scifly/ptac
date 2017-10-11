<?php
namespace App\Models;

use App\Events\CompanyCreated;
use App\Events\CompanyDeleted;
use App\Events\CompanyUpdated;
use App\Facades\DatatableFacade as Datatable;
use App\Helpers\ModelTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Company 运营者公司
 *
 * @property int $id
 * @property string $name 运营者公司名称
 * @property string $remark 运营者公司备注
 * @property string $corpid 与运营者公司对应的企业号id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property int $enabled
 * @method static Builder|Company whereCorpid($value)
 * @method static Builder|Company whereCreatedAt($value)
 * @method static Builder|Company whereEnabled($value)
 * @method static Builder|Company whereId($value)
 * @method static Builder|Company whereName($value)
 * @method static Builder|Company whereRemark($value)
 * @method static Builder|Company whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read Collection|Company[] $corps
 * @property-read Collection|Operator[] $operators
 * @property-read Collection|School[] $schools
 * @property int $department_id 对应的部门ID
 * @property-read \App\Models\Department $department
 * @method static Builder|Company whereDepartmentId($value)
 * @property int $menu_id 对应的菜单ID
 * @property-read Menu $menu
 * @method static Builder|Company whereMenuId($value)
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
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function department() { return $this->belongsTo('App\Models\Department'); }
    
    /**
     * 返回对应的菜单对象
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function menu() { return $this->belongsTo('App\Models\Menu'); }
    
    /**
     * 获取指定运营者公司下属的企业对象
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function corps() { return $this->hasMany('App\Models\Corp'); }
    
    /**
     * 通过Corp中间对象获取所有的学校对象
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function schools() {
        
        return $this->hasManyThrough('App\Models\School', 'App\Models\Corp');
        
    }
    
    /**
     * 获取指定运营者公司内部的所有管理/操作员对象
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function operators() { return $this->hasMany('App\Models\Operator'); }
    
    /**
     * 保存运营者
     *
     * @param array $data
     * @param bool $fireEvent
     * @return bool
     */
    public function store(array $data, $fireEvent = false) {
        
        $company = $this->create($data);
        if ($company && $fireEvent) {
            event(new CompanyCreated($company));
            
            return true;
        }
        
        return $company ? true : false;
        
    }
    
    /**
     * 更新运营者
     *
     * @param array $data
     * @param $id
     * @param bool $fireEvent
     * @return bool
     */
    public function modify(array $data, $id, $fireEvent = false) {
        
        $company = $this->find($id);
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
     */
    public function remove($id, $fireEvent = false) {
        
        $company = $this->find($id);
        if (!$company) { return false; }
        $removed = $this->removable($company) ? $company->delete() : false;
        if ($removed && $fireEvent) {
            event(new CompanyDeleted($company));
            return true;
        }
        
        return $removed ? true : false;
        
    }
    
    function datatable() {
        
        $columns = [
            ['db' => 'Company.id', 'dt' => 0],
            ['db' => 'Company.name', 'dt' => 1],
            ['db' => 'Company.remark', 'dt' => 2],
            ['db' => 'Company.created_at', 'dt' => 3],
            ['db' => 'Company.updated_at', 'dt' => 4],
            [
                'db'        => 'Company.enabled', 'dt' => 5,
                'formatter' => function ($d, $row) {
                    return Datatable::dtOps($this, $d, $row);
                }],
        ];
        
        return Datatable::simple($this, $columns);
        
    }
    
}
