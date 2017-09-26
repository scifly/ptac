<?php

namespace App\Models;

use App\Facades\DatatableFacade as Datatable;
use App\Helpers\ModelTrait;
use App\Http\Requests\EducatorRequest;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Mockery\Exception;

/**
 * App\Models\Educator 教职员工
 *
 * @property int $id
 * @property int $user_id 教职员工用户ID
 * @property string $team_ids 所属组
 * @property int $school_id 所属学校ID
 * @property int $sms_quote 可用短信条数
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property int $enabled
 * @method static Builder|Educator whereCreatedAt($value)
 * @method static Builder|Educator whereId($value)
 * @method static Builder|Educator whereSchoolId($value)
 * @method static Builder|Educator whereSmsQuote($value)
 * @method static Builder|Educator whereTeamIds($value)
 * @method static Builder|Educator whereUpdatedAt($value)
 * @method static Builder|Educator whereUserId($value)
 * @method static Builder|Educator whereEnabled($value)
 * @mixin \Eloquent
 * @property-read User $user
 * @property-read School $school
 * @property-read Squad[] $classes
 * @property-read EducatorClass $educatorClass
 * @property-read Team[] $teams
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\EducatorClass[] $educatorClasses
 */
class Educator extends Model {
    
    use ModelTrait;
    
    
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
                        $editLink . Datatable::DT_SPACE . $delLink . Datatable::DT_SPACE . $rechargeLink;
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
        
        return Datatable::simple($this, $columns, $joins);
        
    }
    
    /**
     * 保存新创建的教职员工记录
     *
     * @param EducatorRequest|CustodianRequest $request
     * @return bool|mixed
     */
    public function store(EducatorRequest $request) {
        
        try {
            $exception = DB::transaction(function () use ($request) {
//                dd($request->all());
                $userInputData = $request->input('user');
                $userData = [
                    'username' => $userInputData['username'],
                    'group_id' => $userInputData['group_id'],
                    'password' => $userInputData['password'],
                    'email' => $userInputData['email'],
                    'realname' => $userInputData['realname'],
                    'gender' => $userInputData['gender'],
                    'avatar_url' => '00001.jpg',
                    'userid' => "11111",
                    'isleader' => 0,
                    'english_name' => $userInputData['english_name'],
                    'telephone' => $userInputData['telephone'],
                    'wechatid' => '',
                    'enabled' => $userInputData['enabled']
                ];
                $user = new User();
                $u = $user->create($userData);
                
                $selectedDepartments = $request->input('selectedDepartments');
                if (!empty($selectedDepartments)) {
                    $departmentUserModel = new DepartmentUser();
                    foreach ($selectedDepartments as $department) {
                        $departmentData = [
                            'user_id' => $u->id,
                            'department_id' => $department,
                            'enabled' => $userInputData['enabled']
                        ];
                        $departmentUserModel->create($departmentData);
                    }
                    unset($departmentUserModel);
                }
                
                $educatorInputData = $request->input('educator');
                $educatorData = [
                    'user_id' => $u->id,
                    'school_id' => $educatorInputData['school_id'],
                    'sms_quote' => 0,
                    'enabled' => $userInputData['enabled']
                ];
                
                $educator = $this->create($educatorData);
                
                $teamIds = $educatorInputData['team_id'];
                if ($teamIds) {
                    $edTeam = new EducatorTeam();
                    foreach ($teamIds as $key => $row) {
                        $edData = [
                            'educator_id' => $educator->id,
                            'team_id' => $row,
                            'enabled' => $userInputData['enabled']
                        ];
                        $edTeam->create($edData);
                    }
                    unset($edTeam);
                }
                
                $classSubjectData = $request->input('classSubject');
                if ($classSubjectData) {
                    $educatorClass = new EducatorClass();
                    $uniqueArray = [];
                    
                    foreach ($classSubjectData['class_ids'] as $index => $class) {
                        $uniqueArray[] = [
                            'class_id' => $class,
                            'subject_id' => $classSubjectData['subject_ids'][$index],
                        ];
                    }
                    $classSubjects = $this->array_unique_fb($uniqueArray);
                    
                    foreach ($classSubjects as $key => $row) {
                        if ($row['class_id'] != 0 && $row['class_id'] != 0) {
                            $educatorClassData = [
                                'educator_id' => $educator->id,
                                'class_id' => $row['class_id'],
                                'subject_id' => $row['subject_id'],
                                'enabled' => $userInputData['enabled']
                            ];
                            
                            $educatorClass->create($educatorClassData);
                            
                        }
                        
                    }
                    
                    unset($educatorClass);
                }
                
                $mobiles = $request->input('mobile');
                if ($mobiles) {
                    $mobileModel = new Mobile();
                    foreach ($mobiles as $k => $mobile) {
                        $mobileData = [
                            'user_id' => $u->id,
                            'mobile' => $mobile['mobile'],
                            'isdefault' => $mobile['isdefault'],
                            'enabled' => $mobile['enabled'],
                        ];
                        $mobileModel->create($mobileData);
                    }
                    unset($mobile);
                }

                # 创建企业号成员
                $user->createWechatUser($u->id);
                unset($user);
            });
            return is_null($exception) ? true : $exception;
        } catch (Exception $e) {
            return false;
        }
        
    }
    //二维数组去掉重复值
    function array_unique_fb($array2D) {
        foreach ($array2D as $v) {
            $v = join(',', $v); //降维,也可以用implode,将一维数组转换为用逗号连接的字符串
            $temp[] = $v;
        }
        $tempUnique = array_unique($temp); //去掉重复的字符串,也就是重复的一维数组
        $csArray = [];
        foreach ($tempUnique as $k => $v) {
            $tempArray = explode(',', $v); //再将拆开的数组重新组装
            $csArray[$k]['class_id'] = $tempArray[0];
            $csArray[$k]['subject_id'] = $tempArray[1];
        }
        return $csArray;
    }

    public function modify(EducatorRequest $request) {
        
        try {
            $exception = DB::transaction(function () use ($request) {

//                dd($request->all());die;
                
                $userInputData = $request->input('user');
                $userData = [
                    'username' => $userInputData['username'],
                    'group_id' => $userInputData['group_id'],
                    'email' => $userInputData['email'],
                    'realname' => $userInputData['realname'],
                    'gender' => $userInputData['gender'],
                    'avatar_url' => '00001.jpg',
                    'userid' => uniqid('custodian_'),
                    'isleader' => 0,
                    'english_name' => $userInputData['english_name'],
                    'telephone' => $userInputData['telephone'],
                    'wechatid' => '',
                    'enabled' => $userInputData['enabled']
                ];
                $user = new User();
                $u = $user->where('id', $request->input('user_id'))->update($userData);
                
                $selectedDepartments = $request->input('selectedDepartments');
                if (!empty($selectedDepartments)) {
                    $departmentUserModel = new DepartmentUser();
                    $departmentUserModel->where('user_id', $request->input('user_id'))->delete();
                    foreach ($selectedDepartments as $department) {
                        $departmentData = [
                            'user_id' => $request->input('user_id'),
                            'department_id' => $department,
                            'enabled' => $userInputData['enabled']
                        
                        ];
                        $departmentUserModel->create($departmentData);
                    }
                    unset($departmentUserModel);
                }
                
                
                $educator = $request->input('educator');
                $educatorData = [
                    'user_id' => $request->input('user_id'),
                    'school_id' => $educator['school_id'],
                    'sms_quote' => 0,
                    'enabled' => $userInputData['enabled']
                ];
                $educatorUpdate = $this->where('id', $request->input('id'))->update($educatorData);
                
                $teamIds = $educator['team_id'];
                if ($teamIds) {
                    $edTeam = new EducatorTeam();
                    $edTeam->where('educator_id', $request->input('id'))->delete();
                    foreach ($teamIds as $key => $row) {
                        $edData = [
                            'educator_id' => $request->input('id'),
                            'team_id' => $row,
                            'enabled' => $userInputData['enabled']
                        ];
                        $edTeam->create($edData);
                    }
                    unset($edTeam);
                }
                
                
                $classSubject = $request->input('classSubject');
                if ($classSubject) {
                    $educatorClass = new EducatorClass();
                    $educatorClass->where('educator_id', $request->input('id'))->delete();
                    $classSubject = $this->array_unique_fb($classSubject);
                    foreach ($classSubject as $key => $row) {
                        if ($row['class_id'] != "" && $row['class_id'] != "") {
                            $educatorClassData = [
                                'educator_id' => $request->input('id'),
                                'class_id' => $row['class_id'],
                                'subject_id' => $row['subject_id'],
                                'enabled' => $userInputData['enabled']
                            ];
                            $educatorClass->create($educatorClassData);
                        }
                        
                    }
                    
                    unset($educatorClass);
                }
                
                $mobiles = $request->input('mobile');
                if ($mobiles) {
                    $mobileModel = new Mobile();
                    $mobileModel->where('user_id', $request->input('user_id'))->delete();
                    foreach ($mobiles as $k => $mobile) {
                        $mobileData = [
                            'user_id' => $request->input('user_id'),
                            'mobile' => $mobile['mobile'],
                            'isdefault' => $mobile['isdefault'],
                            'enabled' => $mobile['enabled'],
                        ];
                        $mobileModel->create($mobileData);
                    }
                    
                    unset($mobile);
                }

                # 更新企业号成员
                $user->UpdateWechatUser($request->input('user_id'));
                unset($user);
                
            });
            return is_null($exception) ? true : $exception;
        } catch (Exception $e) {
            return false;
        }
        
    }
    
    /**
     * 删除教职员工
     *
     * @param $id
     * @param bool $fireEvent
     * @return bool
     */
    public function remove($id, $fireEvent = false) {
        
        $school = $this->find($id);
        $removed = $this->removable($school) ? $school->delete() : false;
        if ($removed && $fireEvent) {
//            event(new SchoolDeleted($school));
            return true;
        }
        return $removed ? true : false;
        
    }
}

