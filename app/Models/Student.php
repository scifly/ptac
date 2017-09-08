<?php

namespace App\Models;

use App\Facades\DatatableFacade as Datatable;
use App\Http\Requests\StudentRequest;
use App\Models\CustodianStudent;
use App\Models\Score;
use App\Models\ScoreTotal;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;


/**
 * App\Models\Student
 *
 * @property int $id
 * @property int $user_id 用户ID
 * @property int $class_id 班级ID
 * @property string $student_number 学号
 * @property string $card_number 卡号
 * @property int $oncampus 是否住校
 * @property string $birthday 生日
 * @property string $remark 备注
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @method static Builder|Student whereBirthday($value)
 * @method static Builder|Student whereCardNumber($value)
 * @method static Builder|Student whereClassId($value)
 * @method static Builder|Student whereCreatedAt($value)
 * @method static Builder|Student whereId($value)
 * @method static Builder|Student whereOncampus($value)
 * @method static Builder|Student whereRemark($value)
 * @method static Builder|Student whereStudentNumber($value)
 * @method static Builder|Student whereUpdatedAt($value)
 * @method static Builder|Student whereUserId($value)
 * @mixin \Eloquent
 * @property int $enabled
 * @property-read \App\Models\Squad $beLongsToSquad
 * @property-read Collection|CustodianStudent[] $custodianStudent
 * @property-read Collection|Score[] $score
 * @property-read Collection|ScoreTotal[] $scoreTotal
 * @property-read \App\Models\Squad $squad
 * @method static Builder|Student whereEnabled($value)
 * @property-read Collection|Custodian[] $custodians
 * @property-read Collection|ScoreTotal[] $scoreTotals
 * @property-read Collection|Score[] $scores
 */
class Student extends Model {
    
    protected $fillable = [
        'user_id', 'class_id', 'student_number',
        'card_number', 'oncampus', 'birthday',
        'remark', 'enabled'
    ];
    
    /**
     * 返回指定学生所属的班级对象
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function squad() { return $this->belongsTo('App\Models\Squad', 'class_id', 'id'); }
    
    /**
     * 获取指定学生的所有监护人对象
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function custodians() {
        
        return $this->belongsToMany('App\Models\Custodian', 'custodians_students');
        
    }
    
    /**
     * 获取指定学生对应的用户对象
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user() { return $this->belongsTo('App\Models\User'); }
    
    /**
     * 获取指定学生所有的分数对象
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function scores() { return $this->hasMany('App\Models\Score'); }
    
    /**
     * 获取指定学生所有的总分对象
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function scoreTotals() { return $this->hasMany('App\Models\ScoreTotal'); }
    
    /**
     * 返回学生列表
     *
     * @param array $classIds
     * @return array
     */
    public function students(array $classIds = []) {

        $studentList = [];
        if (empty($classIds)) {
            $students = $this->all();
        } else {
            $students = $this->whereIn('class_id', $classIds)->get();
        }
        foreach ($students as $student) {
            $studentList[$student->id] = $student->user->realname;
        }
        return $studentList;
    
    }


    /**
     * 保存新创建的监护人记录
     *
     * @param \App\Models\StudentRequest $request
     * @return bool|mixed
     */
    public function store(StudentRequest $request) {

        try {
            $exception = DB::transaction(function() use ($request) {
                $user = $request->input('user');
                $userData = [
                    'username' => uniqid('custodian_'),
                    'group_id' => $user['group_id'],
                    'password' => 'custodian8888',
                    'email' => $user['email'],
                    'realname' => $user['realname'],
                    'gender' => $user['gender'],
                    'avatar_url' => '00001.jpg',
                    'userid' => uniqid('custodian_'),
                    'isleader' => 0,
                    'english_name'=>$user['english_name'],
                    'telephone' => $user['telephone'],
                    'wechatid' => '',
                    'enabled' =>$user['enabled']
                ];
                $user = new User();
                $u = $user->create($userData);
                unset($user);
                $student = $request->input('student');
                $studentData = [
                    'user_id' => $u->id,
                    'class_id' => $student['class_id'],
                    'student_number' => $student['student_number'],
                    'card_number' => $student['card_number'],
                    'oncampus' => $student['oncampus'],
                    'birthday' => $student['birthday'],
                    'remark' => $student['remark'],
                    'enabled' => 1
                ];
                $mobileData = [
                    'user_id' => $u->id,
                    'mobile' =>$request->input('mobile')['mobile'],
                    'enabled' => 1,
                    'isdefault' => 1,
                ];
                # 向student表添加数据
                $Student = new Student();
                $s = $Student->create($studentData);
                unset($Student);

                # 向mobile表添加用户的手机数据
                $mobile = new Mobile();
                $m = $mobile->create($mobileData);
                unset($mobile);

                # 向部门用户表添加数据
                $departmentUser = new DepartmentUser();
                $departmentIds = $request->input('department_ids');
                $departmentUser ->storeByDepartmentId($u->id, $departmentIds);
                unset($departmentUser);

                # 向监护人学生表中添加数据
                $custodianStudent = new CustodianStudent();
                $custodianIds = $request->input('custodian_ids');
                $custodianStudent->storeByStudentId($s->id, $custodianIds);
                unset($custodianStudent);
            });
            return is_null($exception) ? true : $exception;
        } catch (Exception $e) {
            return false;
        }

    }


    /**
     * 返回学生学号姓名列表
     *
     * @param $classIds
     * @return array
     */
    public function studentsNum($classIds) {

        $studentList = [];
        $students = $this->whereIn('class_id', explode(',', $classIds))->get();
        foreach ($students as $student) {
            $studentList[] = [$student->student_number, $student->user->realname];
        }
        return $studentList;

    }
    

    /**
     * 更新指定的学生记录
     *
     * @param StudentRequest $request
     * @param $studentId
     * @return bool|mixed
     */
    public function modify(StudentRequest $request, $studentId) {

        $student = $this->find($studentId);
        if (!isset($student)) { return false; }
        try {
            $exception = DB::transaction(function() use($request, $studentId, $student) {
                $userId = $request->input('user_id');
                $userData = $request->input('user');
                $user = new User();
                $user->where('id',$userId)
                    ->update([
                        'group_id' => $userData['group_id'],
                        'email' => $userData['email'],
                        'realname' => $userData['realname'],
                        'gender' => $userData['gender'],
                        'isleader' => 0,
                        'english_name'=>$userData['english_name'],
                        'telephone' => $userData['telephone'],
                        'enabled' =>$userData['enabled']
                    ]);
                unset($user);
                $studentData = $request->input('student');
                $student->update([
                    'user_id' => $userId,
                    'class_id' => $studentData['class_id'],
                    'student_number' => $studentData['student_number'],
                    'card_number' => $studentData['card_number'],
                    'oncampus' => $studentData['oncampus'],
                    'birthday' => $studentData['birthday'],
                    'remark' => $studentData['remark'],
                    'enabled' => 1
                ]);
                $mobile = new Mobile();
                $mobile->where('user_id',$userId)
                    ->update([
                        'user_id' => $userId,
                        'mobile' => $request->input('mobile')['mobile'],
                    ]);
                unset($mobile);
                $departmentIds = $request->input('department_ids');
                $departmentUser = new DepartmentUser();
                $departmentUser::where('user_id',$userId)->delete();
                $departmentUser ->storeByDepartmentId($userId, $departmentIds);
                unset($departmentUser);
                $custodianStudent = new CustodianStudent();
                $custodianIds = $request->input('custodian_ids');
                $custodianStudent::where('student_id',$studentId)->delete();
                $custodianStudent->storeByStudentId($studentId, $custodianIds);
                unset($custodianStudent);
            });

            return is_null($exception) ? true : $exception;
        } catch (Exception $e) {
            return false;
        }

    }

    /**
     * 删除指定的学生记录
     *
     * @param $studentId
     * @return bool|mixed
     */
    public function remove($studentId)
    {

        $student = $this->find($studentId);

        if (!isset($custodian)) {
            return false;
        }
        try {
            $exception = DB::transaction(function () use ($studentId, $student) {
                # 删除指定的监护人记录
                $student->delete();
                # 删除与指定监护人绑定的监护人记录
                CustodianStudent::where('student_id', $studentId)->delete();
                # 删除与指定监护人绑定的部门记录
                DepartmentUser::where('user_id', $student['user_id'])->delete();
                # 删除与指定监护人绑定的手机记录
                Mobile::where('user_id', $student['user_id'])->delete();

            });

            return is_null($exception) ? true : $exception;
        } catch (Exception $e) {
            return false;
        }
    }


        public function datatable() {
        
        $columns = [
            ['db' => 'Student.id', 'dt' => 0],
            ['db' => 'User.realname as username', 'dt' => 1],
            ['db' => 'Squad.name as classname', 'dt' => 2],
            ['db' => 'Student.student_number', 'dt' => 3],
            ['db' => 'Student.card_number', 'dt' => 4],
            [
                'db' => 'Student.oncampus', 'dt' => 5,
                'formatter' => function ($d) {
                    $student = Student::whereId($d)->first();
                    return $student->oncampus == 1 ? '是' : '否';
                }
            ],
            ['db' => 'Student.birthday', 'dt' => 6],
            ['db' => 'Student.remark', 'dt' => 7],
            ['db' => 'Student.created_at', 'dt' => 8],
            ['db' => 'Student.updated_at', 'dt' => 9],
            [
                'db' => 'Student.enabled', 'dt' => 10,
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
                    'User.id = Student.user_id'
                ]
            ],
            [
                'table' => 'classes',
                'alias' => 'Squad',
                'type' => 'INNER',
                'conditions' => [
                    'Squad.id = Student.class_id'
                ]
            ]
        
        ];
        
        return Datatable::simple($this, $columns, $joins);
        
    }
    
}
