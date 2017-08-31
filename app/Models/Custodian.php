<?php

namespace App\Models;

use App\Facades\DatatableFacade as Datatable;
use App\Models\CustodianStudent;
use App\Models\Student;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use App\Http\Requests\CustodianRequest;
use Illuminate\Support\Facades\DB;
use Mockery\Exception;

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
 * @property-read Collection|Student[] $students
 * @property-read Collection|CustodianStudent[] $custodianStudent
 */
class Custodian extends Model {
    
    protected $fillable = ['user_id', 'expiry'];
    
    /**
     * 返回对应的用户对象
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user() { return $this->belongsTo('App\Models\User'); }
    
    /**
     * 返回对应的学生对象
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function students() { return $this->belongsToMany('App\Models\Student'); }
    
    /**
     * 判断监护人记录是否存在
     *
     * @param CustodianRequest $request
     * @param null $id
     * @return bool
     */
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
    
    /**
     * 保存新创建的监护人记录
     *
     * @param CustodianRequest $request
     * @return bool|mixed
     */
    public function store(CustodianRequest $request) {
    
        try {
            $exception = DB::transaction(function() use ($request) {
                $userData = [
                    'username' => uniqid('custodian_'),
                    'group_id' => $request->input('group_id'),
                    'password' => 'custodian8888',
                    'email' => $request->input('email'),
                    'realname' => $request->input('realname'),
                    'gender' => $request->input('gender'),
                    'avatar_url' => '',
                    'userid' => $request->input('userid'),
                    'department_ids' => implode(',', $request->input('department_ids')),
                    'mobile' => $request->input('mobile'),
                    'isleader' => 0,
                    'wechatid' => ''
                ];
                $user = new User();
                $u = $user->create($userData);
                unset($user);
                $custodianData = [
                    'user_id' => $u->id,
                    'expiry' => time()
                ];
                $c = $this->create($custodianData);
                $custodianStudent = new CustodianStudent();
                $studentIds = $request->input('student_ids', []);
                $custodianStudent->storeByCustodianId($c->id, $studentIds);
                unset($custodianStudent);
            });
            return is_null($exception) ? true : $exception;
        } catch (Exception $e) {
            return false;
        }
    
    }
    
    /**
     * 更新指定的监护人记录
     *
     * @param CustodianRequest $request
     * @param $custodianId
     * @return bool|mixed
     */
    public function modify(CustodianRequest $request, $custodianId) {
    
        $custodian = $this->find($custodianId);
        if (!isset($custodian)) { return false; }
        try {
            $exception = DB::transaction(function() use($request, $custodianId, $custodian) {
                $userId = $request->input('user_id');
                $user = new User();
                $user->update([
                    'username' => $request->input('username'),
                    'email' => $request->input('email'),
                    'department_ids' => implode(',', $request->input('department_ids')),
                    'mobile' => $request->input('mobile'),
                    'wechatid' => $request->input('wechatid')
                ]);
                unset($user);
                $custodian->update([
                    'user_id' => $userId,
                    'expiry' => $request->input('expiry')
                ]);
                $studentIds = $request->input('student_ids', []);
                $custodianStudent = new CustodianStudent();
                $custodianStudent::whereCustodianId($custodianId)->delete();
                $custodianStudent->storeByCustodianId($custodianId, $studentIds);
                unset($custodianStudent);
            });
        
            return is_null($exception) ? true : $exception;
        } catch (Exception $e) {
            return false;
        }
    
    }
    
    /**
     * 删除指定的监护人记录
     *
     * @param $custodianId
     * @return bool|mixed
     */
    public function remove($custodianId) {
    
        $custodian = $this->find($custodianId);
        if (!isset($custodian)) { return false; }
        try {
            $exception = DB::transaction(function() use ($custodianId, $custodian) {
                # 删除指定的监护人记录
                $custodian->delete();
                # 删除与指定监护人绑定的学生记录
                CustodianStudent::whereCustodianId($custodianId)->delete();
            });
        
            return is_null($exception) ? true : $exception;
        } catch (Exception $e) {
            return false;
        }
    
    }
    
    /**
     * 返回监护人记录列表
     *
     * @return array
     */
    public function datatable() {

        $columns = [
            ['db' => 'Custodian.id', 'dt' => 0],
            ['db' => 'User.realname', 'dt' => 1],
            ['db' => 'User.gender', 'dt' => 2],
            ['db' => 'User.email', 'dt' => 3],
            ['db' => 'User.mobile', 'dt' => 4],
            ['db' => 'Custodian.expiry', 'dt' => 5],
            ['db' => 'Custodian.created_at', 'dt' => 6],
            ['db' => 'Custodian.updated_at', 'dt' => 7],
            [
                'db' => 'User.enabled', 'dt' => 8,
                'formatter' => function($d, $row) {
                    return Datatable::dtOps($this, $d, $row);
                }
            ]
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
