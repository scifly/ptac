<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Corp
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
 */
class Corp extends Model {
    
    //
    protected $table = 'corps';
    protected $fillable = [
        'name',
        'corpid',
        'created_at',
        'updated_at',
        'enabled'
    ];

    /**
     * 企业与部门
     */
    public function departments(){
        return $this->hasMany('App\Models\Department');
    }
    public function company(){

        return $this->belongsTo('App\Models\Corp');
    }
}
