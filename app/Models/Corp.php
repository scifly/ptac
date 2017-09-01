<?php

namespace App\Models;

use App\Facades\DatatableFacade as Datatable;
use App\Http\Requests\CompanyRequest;
use App\Http\Requests\CorpRequest;
use App\Models\Department;
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
 */
class Corp extends Model {
    
    protected $fillable = ['name', 'company_id', 'corpid', 'enabled'];
    
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
     * 判断企业对象记录是否已经存在
     *
     * @param CorpRequest $request
     * @param null $id
     * @return bool
     */
    public function existed(CorpRequest $request, $id = NULL) {
        
        if (!$id) {
            $corp = $this->where('name', $request->input('name'))
                ->where('company_id', $request->input('company_id'))->first();
        } else {
            $corp = $this->where('name', $request->input('name'))
                ->where('id', '<>', $id)
                ->where('company_id', $request->input('company_id'))->first();
        }
        
        return $corp ? true : false;
        
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
                'db' => 'Corp.enabled', 'dt' => 6,
                'formatter' => function ($d, $row) {
                    return Datatable::dtOps($this, $d, $row);
                }
            ]
        ];
        $joins = [
            [
                'table' => 'companies',
                'alias' => 'Company',
                'type' => 'INNER',
                'conditions' => [
                    'Company.id = Corp.company_id'
                ]
            ]
        ];
        return Datatable::simple($this, $columns, $joins);
    }
    
}
