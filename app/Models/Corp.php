<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use App\Facades\DatatableFacade as Datatable;

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
class Corp extends Model
{

    protected $fillable = [
        'name',
        'company_id',
        'corpid',
        'enabled'
    ];


    public function departments()
    {
        return $this->hasMany('App\Models\Department');
    }

    public function company()
    {

        return $this->belongsTo('App\Models\Company');
    }

    /**
     * @return mixed
     */
    public function datatable()
    {

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
