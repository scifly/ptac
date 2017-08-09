<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use App\Facades\DatatableFacade as Datatable;

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
 */
class Student extends Model {

    protected $table = 'students';

    protected $fillable = [
        'user_id',
        'class_id',
        'student_number',
        'card_number',
        'oncampus',
        'birthday',
        'remark',
        'enabled'
    ];



    public function squad()
    {
        return $this->belongsTo('App\Models\Squad','class_id','id');
    }


    public function beLongsToSquad() {
        return $this->belongsTo('App\Models\Squad','class_id','id');

    }

    public function custodianStudent()
    {
        return $this->hasMany('App\Models\CustodianStudent');

    }

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }
    /**
     * 获取学生所有分数
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function score()
    {
        return $this->hasMany('App\Models\Score');
    }

    /**
     * 获取学生总分
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function scoreTotal()
    {
        return $this->hasMany('App\Models\ScoreTotal');
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
                'formatter' => function($d) {
                    $student = Student::whereId($d)->first();
                    return $student->oncampus==1 ? '是' : '否' ;
                }
            ],
            ['db' => 'Student.birthday', 'dt' => 6],
            ['db' => 'Student.remark', 'dt' => 7],
            ['db' => 'Student.created_at', 'dt' => 8],
            ['db' => 'Student.updated_at', 'dt' => 9],
            [
                'db' => 'Student.enabled', 'dt' => 10,
                'formatter' => function($d, $row)
                {
                    return Datatable::dtOps($this, $d ,$row);
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



        return Datatable::simple($this, $columns,$joins);

    }









}
