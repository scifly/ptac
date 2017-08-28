<?php

namespace App\Models;

use App\Facades\DatatableFacade as Datatable;
use App\Http\Requests\CompanyRequest;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;


/**
 * App\Models\Company
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
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Company[] $corps
 */
class Company extends Model {
    
    protected $fillable = [
        'name',
        'remark',
        'corpid',
        'enabled'
    ];
    
    public function corps() {
        
        return $this->hasMany('App\Models\Company');
        
    }
    
    public function existed(CompanyRequest $request, $id = NULL) {
        if (!$id) {
            $company = $this->where('name', $request->input('name'))
                ->where('corpid', $request->input('corpid'))->first();
        } else {
            $company = $this->where('name', $request->input('name'))
                ->where('id', '<>', $id)
                ->where('corpid', $request->input('corpid'))->first();
        }
        return $company ? true : false;
        
    }
    
    function datatable() {
        
        $columns = [
            ['db' => 'Company.id', 'dt' => 0],
            ['db' => 'Company.name', 'dt' => 1],
            ['db' => 'Company.remark', 'dt' => 2],
            ['db' => 'Company.corpid as companyCorpid', 'dt' => 3],
            ['db' => 'Company.created_at', 'dt' => 4],
            ['db' => 'Company.updated_at', 'dt' => 5],
            [
                'db' => 'Company.enabled', 'dt' => 6,
                'formatter' => function ($d, $row) {
                    return Datatable::dtOps($this, $d, $row);
                }]
        ];
        return Datatable::simple($this, $columns);
    }
    
    
}
