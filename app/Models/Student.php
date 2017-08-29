<?php

namespace App\Models;

use App\Facades\DatatableFacade as Datatable;
use App\Http\Requests\StudentRequest;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;


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
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\CustodianStudent[] $custodianStudent
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Score[] $score
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ScoreTotal[] $scoreTotal
 * @property-read \App\Models\Squad $squad
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Student whereEnabled($value)
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Custodian[] $custodians
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ScoreTotal[] $scoreTotals
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Score[] $scores
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
    
    
    public function squad() {
        
        return $this->belongsTo('App\Models\Squad', 'class_id', 'id');
        
    }
    
    public function custodians() {
        
        return $this->belongsToMany('App\Models\Custodian', 'custodians_students');
        
    }
    
    public function user() {
        
        return $this->belongsTo('App\Models\User');
        
    }
    
    /**
     * 获取学生所有分数
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function scores() {
        
        return $this->hasMany('App\Models\Score');
        
    }
    
    /**
     * 获取学生总分
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function scoreTotals() {
        
        return $this->hasMany('App\Models\ScoreTotal');
        
    }
    
    public function existed(StudentRequest $request, $id = NULL) {
        
        if (!$id) {
            $student = $this->where('user_id', $request->input('user_id'))
                ->where('class_id', $request->input('class_id'))
                ->where('student_number', $request->input('student_number'))
                ->first();
        } else {
            $student = $this->where('user_id', $request->input('user_id'))
                ->where('id', '<>', $id)
                ->where('class_id', $request->input('class_id'))
                ->where('student_number', $request->input('student_number'))
                ->first();
        }
        return $student ? true : false;
        
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
