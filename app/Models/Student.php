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
        'remark'
    ];
    public function user() { return $this->belongsTo('App\Models\User'); }

    public function custodians() { return $this->belongsToMany('App\Models\Custodian'); }

    public function squad(){ return $this->belongsTo('App\Models\Squad','class_id','id'); }

    public function beLongsToSquad() {

        return $this->belongsTo('App\Models\Squad','class_id','id');

    }

    public function datatable() {

        $columns = [
            ['db' => 'Student.id', 'dt' => 0],
            ['db' => 'User.username as username', 'dt' => 1],
            ['db' => 'Squad.name as classname', 'dt' => 2],
            ['db' => 'Student.card_number', 'dt' => 3],
            ['db' => 'Student.oncampus', 'dt' => 4],
            ['db' => 'Student.birthday', 'dt' => 5],
            ['db' => 'Student.remark', 'dt' => 6],
            ['db' => 'Student.created_at', 'dt' => 7],
            ['db' => 'Student.updated_at', 'dt' => 8,
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
