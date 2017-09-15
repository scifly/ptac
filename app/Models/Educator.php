<?php

namespace App\Models;

use App\Facades\DatatableFacade as Datatable;
use App\Facades\DatatableFacade;
use App\Http\Requests\EducatorRequest;
use App\Models\EducatorClass;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Mockery\Exception;

/**
 * App\Models\Educator
 * 
 * 教职员工
 *
 * @property int $id
 * @property int $user_id 教职员工用户ID
 * @property string $team_ids 所属组
 * @property int $school_id 所属学校ID
 * @property int $sms_quote 可用短信条数
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @method static Builder|Educator whereCreatedAt($value)
 * @method static Builder|Educator whereId($value)
 * @method static Builder|Educator whereSchoolId($value)
 * @method static Builder|Educator whereSmsQuote($value)
 * @method static Builder|Educator whereTeamIds($value)
 * @method static Builder|Educator whereUpdatedAt($value)
 * @method static Builder|Educator whereUserId($value)
 * @mixin \Eloquent
 * @property-read \App\Models\School $school
 * @property int $enabled
 * @property-read Collection|\App\Models\Squad[] $classes
 * @property-read EducatorClass $educatorClass
 * @method static Builder|Educator whereEnabled($value)
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Team[] $teams
 */
class Educator extends Model {
    
    protected $fillable = [
        'user_id', 'team_ids', 'school_id',
        'sms_quote', 'enabled',
    ];
    
    /**
     * 返回指定教职员工对应的用户对象
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user() { return $this->belongsTo('App\Models\User'); }
    
    /**
     * 返回指定教职员工所属的学校对象
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function school() { return $this->belongsTo('App\Models\School'); }
    
    /**
     * 获取指定教职员工所属的所有班级对象
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function classes() {
        
        return $this->belongsToMany(
            'App\Models\Squad',
            'educators_classes',
            'educator_id',
            'class_id'
        );
        
    }
    
    /**
     * 获取指定教职员工所属的所有教职员工组对象
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function teams() {
        
        return $this->belongsToMany(
            'App\Models\Team',
            'educators_teams'
        );
    
    }

    /**
     *  获取指定教职员工的班级科目关系
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function educatorClasses() { return $this->hasMany('App\Models\EducatorClass'); }

    /**
     * 获取指定年级的年级主任教职员工对象
     *
     * @param $gradeId
     * @return Collection|static[]
     */
    public function gradeDeans($gradeId) {

        $educatorIds = Grade::whereId($gradeId)->where('enabled', 1)->first()->educator_ids;
        return $this->whereIn('id', explode(',', $educatorIds))->whereEnabled(1)->get();

    }
    
    /**
     * 获取指定班级的班级主任教职员工对象
     *
     * @param $classId
     * @return Collection|static[]
     */
    public function classDeans($classId) {
        
        $educatorIds = Squad::whereId($classId)->where('enabled', 1)->first()->educator_ids;
        return $this->whereIn('id', explode(',', $educatorIds))->whereEnabled(1)->get();
        
    }
    
    /**
     * 获取指定学校的教职员工列表
     *
     * @param array $schoolIds
     * @return array
     */
    public function educators(array $schoolIds = []) {
        
        $educatorList = [];
        if (empty($schoolIds)) {
            $educators = $this->all();
        } else {
            $educators = $this->whereIn('school_id', $schoolIds)->get();
        }
        foreach ($educators as $educator) {
            $educatorList[$educator->id] = $educator->user->realname;
        }
        return $educatorList;
        
    }
    
    public function datatable() {
        
        $columns = [
            ['db' => 'Educator.id', 'dt' => 0],
            ['db' => 'User.realname as username', 'dt' => 1],
            ['db' => 'School.name', 'dt' => 2],
            ['db' => 'Educator.sms_quote', 'dt' => 3],
            ['db' => 'Educator.created_at', 'dt' => 4],
            ['db' => 'Educator.updated_at', 'dt' => 5],
            [
                'db' => 'Educator.enabled', 'dt' => 6,
                'formatter' => function ($d, $row) {
                    $id = $row['id'];
                    $status = $d ? sprintf(Datatable::DT_ON, '已启用') : sprintf(Datatable::DT_OFF, '未启用');
                    $showLink = sprintf(Datatable::DT_LINK_SHOW, 'show_' . $id);
                    $editLink = sprintf(Datatable::DT_LINK_EDIT, 'edit_' . $id);
                    $delLink = sprintf(Datatable::DT_LINK_DEL, $id);
                    $rechargeLink = sprintf(Datatable::DT_LINK_RECHARGE, 'recharge_' . $id);

                    return $status . Datatable::DT_SPACE . $showLink . Datatable::DT_SPACE .
                        $editLink .  Datatable::DT_SPACE . $delLink .  Datatable::DT_SPACE . $rechargeLink ;
                }
            ]
        ];
        $joins = [
            [
                'table' => 'users',
                'alias' => 'User',
                'type' => 'INNER',
                'conditions' => [
                    'User.id = Educator.user_id'
                ]
            ],
            [
                'table' => 'schools',
                'alias' => 'School',
                'type' => 'INNER',
                'conditions' => [
                    'School.id = Educator.school_id'
                ]
            ]
        ];
        
        return Datatable::simple($this, $columns,$joins);
        
    }
    /**
     * 保存新创建的教职员工记录
     *
     * @param CustodianRequest $request
     * @return bool|mixed
     */
    public function store(EducatorRequest $request) {

        try {
            $exception = DB::transaction(function() use ($request) {
                $userInputData = $request->input('user');
                $userData = [
                    'username' => uniqid('educator_'),
                    'group_id' => $userInputData['group_id'],
                    'password' => $userInputData['password'],
                    'email' => $userInputData['email'],
                    'realname' => $userInputData['realname'],
                    'gender' => $userInputData['gender'],
                    'avatar_url' => '00001.jpg',
                    'userid' => uniqid('custodian_'),
                    'isleader' => 0,
                    'english_name'=>$userInputData['english_name'],
                    'telephone' => $userInputData['telephone'],
                    'wechatid' => '',
                    'enabled' =>$userInputData['enabled']
                ];
                $user = new User();
                $u = $user->create($userData);
                unset($user);

                $educator = $request->input('educator');
                $educatorData = [
                    'user_id' => $u->id,
                    'school_id' => $educator['school_id'],
                    'sms_quote' => 0,
                    'enabled' =>$userInputData['enabled']
                ];
                $educatorId = $this->create($educatorData);
                $classIds = $educator['class_ids'];
                if($classIds) {
                    $educatorClass = new EducatorClass();
                    foreach ($classIds as $key => $classId) {
                        $educatorClassData = [
                            'educator_id' => $educatorId->id,
                            'class_id' => $classId,
                            'subject_id' => $educator['subject_ids'][$key],
                            'enabled' => $userInputData['enabled']
                        ];
                        $educatorClass->create($educatorClassData);
                    }
                    unset($educatorClass);
                }

                $mobiles = $request->input('mobile');
                if($mobiles) {
                    $mobile = new Mobile();
                    foreach ($mobiles['mobile'] as $k => $row) {
                        $mobileData = [
                            'user_id' => $u->id,
                            'mobile' => $row,
                            'enabled' => isset($mobiles['enabled'][$k]) ? 1 : 0,
                            'isdefault' => (isset($mobiles['isdefault']) && $mobiles['isdefault'] == $k) ? 1 : 0,
                        ];
                        $m = $mobile->create($mobileData);
                    }
                    unset($mobile);
                }
            });
            return is_null($exception) ? true : $exception;
        } catch (Exception $e) {
            return false;
        }

    }
    public function modify(EducatorRequest $request) {

        try {
            $exception = DB::transaction(function() use ($request) {
//                dd($request->all());die;
                $userInputData = $request->input('user');
                $userData = [
                    'username' => uniqid('educator_'),
                    'group_id' => $userInputData['group_id'],
                    'password' => $userInputData['password'],
                    'email' => $userInputData['email'],
                    'realname' => $userInputData['realname'],
                    'gender' => $userInputData['gender'],
                    'avatar_url' => '00001.jpg',
                    'userid' => uniqid('custodian_'),
                    'isleader' => 0,
                    'english_name'=>$userInputData['english_name'],
                    'telephone' => $userInputData['telephone'],
                    'wechatid' => '',
                    'enabled' =>$userInputData['enabled']
                ];
                $user = new User();
                $u = $user->where('id', $request->input('user_id'))->update($userData);
                unset($user);

                $educator = $request->input('educator');
                $educatorData = [
                    'user_id' => $request->input('user_id'),
                    'school_id' => $educator['school_id'],
                    'sms_quote' => 0,
                    'enabled' =>$userInputData['enabled']
                ];
                $educatorUpdate = $this->where('id', $request->input('id'))->update($educatorData);
                $classIds = $educator['class_ids'];
                if($classIds) {
                    $educatorClass = new EducatorClass();
                    $delEducatorClass = $educatorClass->where('educator_id', $request->input('id'))->delete();
                    if($delEducatorClass) {
                        foreach ($classIds as $key => $classId) {
                            $educatorClassData = [
                                'educator_id' => $request->input('id'),
                                'class_id' => $classId,
                                'subject_id' => $educator['subject_ids'][$key],
                                'enabled' => $userInputData['enabled']
                            ];
                            $educatorClass->create($educatorClassData);
                        }
                    }
                    unset($educatorClass);
                }

                $mobiles = $request->input('mobile');
                if($mobiles) {
                    $mobile = new Mobile();
                    $delMobile = $mobile->where('user_id', $request->input('user_id'))->delete();
                    if($delMobile) {
                        foreach ($mobiles['mobile'] as $k => $row) {
                            $mobileData = [
                                'user_id' => $request->input('user_id'),
                                'mobile' => $row,
                                'enabled' => isset($mobiles['enabled'][$k]) ? 1 : 0,
                                'isdefault' => (isset($mobiles['isdefault']) && $mobiles['isdefault'] == $k) ? 1 : 0,
                            ];
                            $mobile->create($mobileData);
                        }
                    }
                    unset($mobile);
                }
            });
            return is_null($exception) ? true : $exception;
        } catch (Exception $e) {
            return false;
        }

    }
    
}

