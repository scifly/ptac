<?php

namespace App\Models;

use App\Facades\DatatableFacade as Datatable;
use App\Http\Requests\OperatorRequest;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Operator 管理/操作员
 *
 * @property int $id
 * @property int $company_id 所属运营者公司ID
 * @property int $user_id 用户ID
 * @property string $school_ids 可管理的学校ID
 * @property int $type 管理员类型：0 - 我们 1 - 代理人
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static Builder|Operator whereCompanyId($value)
 * @method static Builder|Operator whereCreatedAt($value)
 * @method static Builder|Operator whereId($value)
 * @method static Builder|Operator whereSchoolIds($value)
 * @method static Builder|Operator whereType($value)
 * @method static Builder|Operator whereUpdatedAt($value)
 * @method static Builder|Operator whereUserId($value)
 * @mixin \Eloquent
 * @property-read Company $company
 * @property-read User $user
 */
class Operator extends Model {
    
    protected $fillable = [
        'company_id', 'user_id', 'school_ids',
        'type', 'enabled'
    ];
    
    /**
     * 返回指定管理/操作员所属的运营者公司对象
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function company() { return $this->belongsTo('App\Models\Company'); }
    
    /**
     * 获取指定管理/操作员对应的用户对象
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user() { return $this->belongsTo('App\Models\User'); }
    
    /**
     * 返回指定管理/操作员管理的所有学校对象
     *
     * @param $schoolIds
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function schools($schoolIds) {
        
        return School::whereEnabled(1)->
        whereIn('id', explode(',', $schoolIds))->
        get();
        
    }
    
    public function store(OperatorRequest $request) {
        
        return true;
        
    }
    
    public function modify(OperatorRequest $request, $id) {
        
        return true;
        
    }
    
    public function remove($id) {
        
        return true;
        
    }
    
    public function datatable() {
        
        $columns = [
            ['db' => 'Operator.id', 'dt' => 0],
            ['db' => 'User.realname', 'dt' => 1],
            ['db' => 'User.username', 'dt' => 2],
            ['db' => 'Groups.name as groupname', 'dt' => 3],
            ['db' => 'Company.name as companyname', 'dt' => 4],
            ['db' => 'User.userid', 'dt' => 5],
            ['db' => 'Mobile.mobile', 'dt' => 6],
            ['db' => 'Operator.created_at', 'dt' => 7],
            ['db' => 'Operator.updated_at', 'dt' => 8],
            ['db' => 'User.enabled', 'dt' => 9],
        ];
        $joins = [
            [
                'table' => 'users',
                'alias' => 'User',
                'type' => 'INNER',
                'conditions' => ['User.id = Operator.user_id']
            ],
            [
                'table' => 'companies',
                'alias' => 'Company',
                'type' => 'INNER',
                'conditions' => ['Company.id = Operator.company_id']
            ],
            [
                'table' => 'groups',
                'alias' => 'Groups',
                'type' => 'INNER',
                'conditions' => ['Groups.id = User.group_id']
            ],
            [
                'table' => 'mobiles',
                'alias' => 'Mobile',
                'type' => 'LEFT',
                'conditions' => [
                    'User.id = Mobile.user_id',
                    'Mobile.isdefault = 1'
                ]
            ]
        ];
        
        return Datatable::simple($this, $columns, $joins);
        
    }
    
}
