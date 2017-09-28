<?php
namespace App\Models;

use App\Facades\DatatableFacade as Datatable;
use App\Http\Requests\OperatorRequest;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Mockery\Exception;

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
        'type', 'enabled',
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
        try {
            $exception = DB::transaction(function () use ($request) {
//                dd($request->all());
                $userInputData = $request->input('user');
                $userData = [
                    'username'     => $userInputData['username'],
                    'group_id'     => $userInputData['group_id'],
                    'password'     => $userInputData['password'],
                    'email'        => $userInputData['email'],
                    'realname'     => $userInputData['realname'],
                    'gender'       => $userInputData['gender'],
                    'avatar_url'   => '00001.jpg',
                    'userid'       => "11111",
                    'isleader'     => 0,
                    'english_name' => $userInputData['english_name'],
                    'telephone'    => $userInputData['telephone'],
                    'wechatid'     => '',
                    'enabled'      => $userInputData['enabled'],
                ];
                $user = new User();
                $u = $user->create($userData);
                $selectedDepartments = $request->input('selectedDepartments');
                if (!empty($selectedDepartments)) {
                    $departmentUserModel = new DepartmentUser();
                    foreach ($selectedDepartments as $department) {
                        $departmentData = [
                            'user_id'       => $u->id,
                            'department_id' => $department,
                            'enabled'       => $userInputData['enabled'],
                        ];
                        $departmentUserModel->create($departmentData);
                    }
                    unset($departmentUserModel);
                }
                $operatorInputData = $request->input('operator');
                $operatorData = [
                    'user_id'    => $u->id,
                    'company_id' => $operatorInputData['company_id'],
                    'school_ids' => $operatorInputData['school_ids'],
                    'type'       => 0,
                ];
                $operator = $this->create($operatorData);
                $mobiles = $request->input('mobile');
                if ($mobiles) {
                    $mobileModel = new Mobile();
                    foreach ($mobiles as $k => $mobile) {
                        $mobileData = [
                            'user_id'   => $u->id,
                            'mobile'    => $mobile['mobile'],
                            'isdefault' => $mobile['isdefault'],
                            'enabled'   => $mobile['enabled'],
                        ];
                        $mobileModel->create($mobileData);
                    }
                    unset($mobile);
                }
                # 创建企业号成员
                $user->createWechatUser($u->id);
            });

            return is_null($exception) ? true : $exception;
        } catch (Exception $e) {
            return false;
        }

    }

    public function modify(OperatorRequest $request, $id) {

        try {
            $exception = DB::transaction(function () use ($request) {

//                dd($request->all());die;
                $userInputData = $request->input('user');
                $userData = [
                    'username'     => $userInputData['username'],
                    'group_id'     => $userInputData['group_id'],
                    'email'        => $userInputData['email'],
                    'realname'     => $userInputData['realname'],
                    'gender'       => $userInputData['gender'],
                    'avatar_url'   => '00001.jpg',
                    'userid'       => uniqid('custodian_'),
                    'isleader'     => 0,
                    'english_name' => $userInputData['english_name'],
                    'telephone'    => $userInputData['telephone'],
                    'wechatid'     => '',
                    'enabled'      => $userInputData['enabled'],
                ];
                $user = new User();
                $u = $user->where('id', $request->input('user_id'))->update($userData);
                $selectedDepartments = $request->input('selectedDepartments');
                if (!empty($selectedDepartments)) {
                    $departmentUserModel = new DepartmentUser();
                    $departmentUserModel->where('user_id', $request->input('user_id'))->delete();
                    foreach ($selectedDepartments as $department) {
                        $departmentData = [
                            'user_id'       => $request->input('user_id'),
                            'department_id' => $department,
                            'enabled'       => $userInputData['enabled'],
                        ];
                        $departmentUserModel->create($departmentData);
                    }
                    unset($departmentUserModel);
                }
                $operatorInputData = $request->input('operator');
                $operatorData = [
                    'user_id'    => $request->input('user_id'),
                    'company_id' => $operatorInputData['company_id'],
                    'school_ids' => $operatorInputData['school_ids'],
                    'type'       => 0,
                ];
                $operatorUpdate = $this->where('id', $request->input('id'))->update($operatorData);
                $mobiles = $request->input('mobile');
                if ($mobiles) {
                    $mobileModel = new Mobile();
                    $mobileModel->where('user_id', $request->input('user_id'))->delete();
                    foreach ($mobiles as $k => $mobile) {
                        $mobileData = [
                            'user_id'   => $request->input('user_id'),
                            'mobile'    => $mobile['mobile'],
                            'isdefault' => $mobile['isdefault'],
                            'enabled'   => $mobile['enabled'],
                        ];
                        $mobileModel->create($mobileData);
                    }
                    unset($mobile);

                }
                # 创建企业号成员
                $user->UpdateWechatUser($request->input('user_id'));
                unset($user);

            });

            return is_null($exception) ? true : $exception;
        } catch (Exception $e) {
            return false;
        }

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
            [
                'db'        => 'User.enabled', 'dt' => 9,
                'formatter' => function ($d, $row) {
                    return Datatable::dtOps($this, $d, $row);
                },
            ],
        ];
        $joins = [
            [
                'table'      => 'users',
                'alias'      => 'User',
                'type'       => 'INNER',
                'conditions' => ['User.id = Operator.user_id'],
            ],
            [
                'table'      => 'companies',
                'alias'      => 'Company',
                'type'       => 'INNER',
                'conditions' => ['Company.id = Operator.company_id'],
            ],
            [
                'table'      => 'groups',
                'alias'      => 'Groups',
                'type'       => 'INNER',
                'conditions' => ['Groups.id = User.group_id'],
            ],
            [
                'table'      => 'mobiles',
                'alias'      => 'Mobile',
                'type'       => 'LEFT',
                'conditions' => [
                    'User.id = Mobile.user_id',
                    'Mobile.isdefault = 1',
                ],
            ],
        ];

        return Datatable::simple($this, $columns, $joins);

    }

}
