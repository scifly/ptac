<?php

namespace App\Models;

use App\Facades\DatatableFacade as Datatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use App\Http\Requests\CustodianRequest;

/**
 * App\Models\Custodian
 *
 * @property int $id
 * @property int $user_id 监护人用户ID
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property string $expiry 服务到期时间
 * @property-read \App\Models\User $user
 * @method static Builder|Custodian whereCreatedAt($value)
 * @method static Builder|Custodian whereExpiry($value)
 * @method static Builder|Custodian whereId($value)
 * @method static Builder|Custodian whereUpdatedAt($value)
 * @method static Builder|Custodian whereUserId($value)
 * @mixin \Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Student[] $students
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\CustodianStudent[] $custodianStudent
 */
class Custodian extends Model {
    protected $table = 'custodians';
    protected $fillable = [
        'user_id',
        'expiry'
    ];
    
    public function user() {
        return $this->belongsTo('App\Models\User');
    }
    
    
    public function custodianStudent() {
        return $this->hasMany('App\Models\CustodianStudent');
    }

    public function existed(CustodianRequest $request, $id = NULL) {

        if (!$id) {
            $custodian = $this->where('user_id',$request->input('user_id'))
                ->first();
        } else {
            $custodian = $this->where('user_id',$request->input('user_id'))
                ->where('id','<>',$id)
                ->first();
        }
        return $custodian ? true : false;

    }

    public function datatable() {

        $columns = [
            ['db' => 'Custodian.id', 'dt' => 0],
            ['db' => 'User.realname as custodianname', 'dt' => 1],
            ['db' => 'Custodian.expiry', 'dt' => 2],
            ['db' => 'Custodian.created_at', 'dt' => 3],
            [
                'db' => 'Custodian.updated_at', 'dt' => 4,
                'formatter' => function ($d, $row) {
                    return Datatable::dtOps($this, $d, $row);
                }
            ],

        ];

        $joins = [
            [
                'table' => 'users',
                'alias' => 'User',
                'type' => 'INNER',
                'conditions' => [
                    'User.id = Custodian.user_id'
                ]
            ],

        ];

        return Datatable::simple($this, $columns, $joins);

    }
    
}
