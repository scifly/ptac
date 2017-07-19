<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use App\Facades\DatatableFacade as Datatable;
use Illuminate\Http\Request;

/**
 * App\Models\School
 *
 * @property int $id
 * @property int $school_type_id 学校类型ID
 * @property string $name 学校名称
 * @property string $address 学校地址
 * @property float $longitude 学校所处经度
 * @property float $latitude 学校所处纬度
 * @property int $corp_id 学校所属企业ID
 * @property int $sms_max_cnt 学校短信配额
 * @property int $sms_used 短信已使用量
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property int $enabled
 * @method static Builder|School whereAddress($value)
 * @method static Builder|School whereCorpId($value)
 * @method static Builder|School whereCreatedAt($value)
 * @method static Builder|School whereEnabled($value)
 * @method static Builder|School whereId($value)
 * @method static Builder|School whereLatitude($value)
 * @method static Builder|School whereLongitude($value)
 * @method static Builder|School whereName($value)
 * @method static Builder|School whereSchoolTypeId($value)
 * @method static Builder|School whereSmsMaxCnt($value)
 * @method static Builder|School whereSmsUsed($value)
 * @method static Builder|School whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class School extends Model {

    protected $fillable = [
        'name',
        'address',
        'school_type_id',
        'corp_id',
        'enabled'
    ];
    public function semesters()
    {
        return $this->hasMany('App\Models\Semester','school_id','id');
    }
    
    public function schoolType() {
        
        return $this->belongsTo('App\Models\SchoolType');
        
    }
    
    public function corp() {
        
        return $this->belongsTo('App\Models\Corp');
        
    }
    
    public function datatable(Request $request) {
        
        $columns = [
            ['db' => 'School.id', 'dt' => 0],
            ['db' => 'SchoolType.name', 'dt' => 1],
            ['db' => 'School.address', 'dt' => 2],
            ['db' => 'Corp.name', 'dt' => 3],
            ['db' => 'School.created_at', 'dt' => 4],
            ['db' => 'School.updated_at', 'dt' => 5],
            [
                'db' => 'School.enabled', 'dt' => 6,
                'formatter' => function($d, $row) {
                    return Datatable::dtOps($this, $d, $row);
                }
            ]
        ];
        return Datatable::simple($this, $request, $columns);
        
    }
    

    
}
