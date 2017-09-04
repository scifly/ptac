<?php

namespace App\Models;

use App\Facades\DatatableFacade as Datatable;
use App\Http\Requests\CompanyRequest;
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
 */
class Company extends Model {
    
    protected $fillable = ['name', 'remark', 'enabled'];
    
    /**
     * 获取指定运营者公司下属的企业对象
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function corps() { return $this->hasMany('App\Models\Company'); }
    
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
    
    function datatable() {
        
        $columns = [
            ['db' => 'Company.id', 'dt' => 0],
            ['db' => 'Company.name', 'dt' => 1],
            ['db' => 'Company.remark', 'dt' => 2],
            ['db' => 'Company.created_at', 'dt' => 3],
            ['db' => 'Company.updated_at', 'dt' => 4],
            [
                'db' => 'Company.enabled', 'dt' => 5,
                'formatter' => function ($d, $row) {
                    return Datatable::dtOps($this, $d, $row);
                }]
        ];
        return Datatable::simple($this, $columns);
        
    }
    
}
