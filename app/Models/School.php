<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

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
    
}
