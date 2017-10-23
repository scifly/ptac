<?php
namespace App\Models;

use App\Events\CorpCreated;
use App\Events\CorpDeleted;
use App\Events\CorpUpdated;
use App\Facades\DatatableFacade as Datatable;
use App\Helpers\ModelTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Corp 企业
 *
 * @property int $id
 * @property string $name 企业名称
 * @property string $corpid 企业号id
 * @property string $corpsecret 管理组的凭证密钥
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property int $enabled
 * @method static Builder|Corp whereCorpid($value)
 * @method static Builder|Corp whereCorpsecret($value)
 * @method static Builder|Corp whereCreatedAt($value)
 * @method static Builder|Corp whereEnabled($value)
 * @method static Builder|Corp whereId($value)
 * @method static Builder|Corp whereName($value)
 * @method static Builder|Corp whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property int $company_id 所属运营者公司ID
 * @property-read \App\Models\Company $company
 * @property-read Collection|Department[] $departments
 * @method static Builder|Corp whereCompanyId($value)
 * @property-read Collection|Grade[] $grades
 * @property-read Collection|School[] $schools
 * @property-read Collection|Team[] $teams
 * @property int $department_id 对应的部门ID
 * @property-read \App\Models\Department $department
 * @method static Builder|Corp whereDepartmentId($value)
 * @property int $menu_id 对应的菜单ID
 * @property-read \App\Models\Menu $menu
 * @method static Builder|Corp whereMenuId($value)
 */
class Corp extends Model {
    
    use ModelTrait;
    
    protected $fillable = [
        'name', 'company_id', 'corpid', 'menu_id',
        'department_id', 'enabled',
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
     * 获取所属运营者公司对象
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function company() { return $this->belongsTo('App\Models\Company'); }
    
    /**
     * 获取下属学校对象
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function schools() { return $this->hasMany('App\Models\School'); }
    
    /**
     * 通过School中间对象获取所有年级对象
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function grades() {
        
        return $this->hasManyThrough('App\Models\Grade', 'App\Models\School');
        
    }
    
    /**
     * 通过School中间对象获取所有部门对象
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function departments() {
        
        return $this->hasManyThrough('App\Models\Department', 'App\Models\School');
        
    }
    
    /**
     * 通过School中间对象获取所有教职员工组对象
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function teams() {
        
        return $this->hasManyThrough('App\Models\Team', 'App\Models\School');
        
    }
    
    /**
     * 保存企业
     *
     * @param array $data
     * @param bool $fireEvent
     * @return bool
     */
    public function store(array $data, $fireEvent = false) {
        
        $corp = $this->create($data);
        if ($corp && $fireEvent) {
            event(new CorpCreated($corp));
            
            return true;
        }
        
        return $corp ? true : false;
        
    }
    
    /**
     * 更新企业
     *
     * @param array $data
     * @param $id
     * @param bool $fireEvent
     * @return bool
     */
    public function modify(array $data, $id, $fireEvent = false) {
        
        $corp = $this->find($id);
        $updated = $corp->update($data);
        if ($updated && $fireEvent) {
            event(new CorpUpdated($corp));
            
            return true;
        }
        
        return $updated ? true : false;
        
    }
    
    /**
     * 删除企业
     *
     * @param $id
     * @param bool $fireEvent
     * @return bool
     */
    public function remove($id, $fireEvent = false) {
        
        $corp = $this->find($id);
        if (!$corp) {
            return false;
        }
        $removed = $this->removable($corp) ? $corp->delete() : false;
        if ($removed && $fireEvent) {
            event(new CorpDeleted($corp));
            
            return true;
        }
        
        return $removed ? true : false;
        
    }
    
    /**
     * @return mixed
     */
    public function datatable() {
        
        $columns = [
            ['db' => 'Corp.id', 'dt' => 0],
            ['db' => 'Corp.name', 'dt' => 1],
            ['db' => 'Company.name as companyname', 'dt' => 2],
            ['db' => 'Corp.corpid', 'dt' => 3],
            ['db' => 'Corp.created_at', 'dt' => 4],
            ['db' => 'Corp.updated_at', 'dt' => 5],
            [
                'db'        => 'Corp.enabled', 'dt' => 6,
                'formatter' => function ($d, $row) {
                    return Datatable::dtOps($this, $d, $row);
                },
            ],
        ];
        $joins = [
            [
                'table'      => 'companies',
                'alias'      => 'Company',
                'type'       => 'INNER',
                'conditions' => [
                    'Company.id = Corp.company_id',
                ],
            ],
        ];
        
        return Datatable::simple($this, $columns, $joins);
    }
    
}
