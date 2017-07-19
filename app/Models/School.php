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
    
    const DT_ON = '<span class="badge badge-primary">%s</span>';
    const DT_OFF = '<span class="badge badge-default">%s</span>';
    const DT_LINK_EDIT = '<!--suppress HtmlUnknownTarget -->
<a href="/%s/edit/%s" class="btn btn-success btn-icon btn-circle btn-xs"><i class="fa fa-edit"></i></a>';
    const DT_LINK_DEL = '<!--suppress HtmlUnknownAnchorTarget -->
<a id="%s" href="#modal-dialog" class="btn btn-danger btn-icon btn-circle btn-xs" data-toggle="modal"><i class="fa fa-times"></i></a>';
    const DT_SPACE = '&nbsp;';
    const DT_PRIMARY = '<span class="badge badge-info">%s</span>';
    const DT_LOCK = '<i class="fa fa-lock"></i>&nbsp;已占用';
    const DT_UNLOCK = '<i class="fa fa-unlock"></i>&nbsp;空闲中';
    
    protected $fillable = [
        'name',
        'address',
        'school_type_id',
        'corp_id',
        'enabled'
    ];
    
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
                    return $this->_dtOps($this, $d, $row);
                }
            ]
        ];
        return Datatable::simple($this, $request, $columns);
        
    }
    
    /**
     * Display data entry operations
     *
     * @param Model $model
     * @param $active
     * @param $row
     * @param bool|true $del - if set to false, do not show delete link
     * @return string
     */
    protected function _dtOps(Model $model, $active, $row, $del = true) {
        
        switch ($model->getTable()) {
            case 'Group': $name = 'Groups'; break;
            case 'Order': $name = 'Orders'; break;
            case 'Table': $name = 'Tables'; break;
            default: $name = $model->getTable(); break;
        }
        
        $id = $row[$name][$model->getKeyName()];
        $status = $active ? __(self::DT_ON, '已启用') : __(self::DT_OFF, '已禁用');
        $editLink = __(self::DT_LINK_EDIT, $model->getTable(), $id);
        $delLink = __(self::DT_LINK_DEL, $id);
        
        return $status . self::DT_SPACE . $editLink . ($del ? self::DT_SPACE . $delLink : '');
        
    }
    
}
