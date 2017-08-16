<?php

namespace App\Models;

use App\Facades\DatatableFacade as Datatable;
use App\Models\Student as Student;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\CustodianStudent
 *
 * @property int $id
 * @property int $custodian_id 监护人ID
 * @property int $student_id 学生ID
 * @property string $relationship 关系
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static Builder|CustodianStudent whereCreatedAt($value)
 * @method static Builder|CustodianStudent whereCustodianId($value)
 * @method static Builder|CustodianStudent whereId($value)
 * @method static Builder|CustodianStudent whereRelationship($value)
 * @method static Builder|CustodianStudent whereStudentId($value)
 * @method static Builder|CustodianStudent whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property int $enabled 是否启用
 * @property-read \App\Models\Custodian $custodian
 * @property-read \App\Models\Student $student
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CustodianStudent whereEnabled($value)
 */
class CustodianStudent extends Model {
    
    protected $table = 'custodians_students';
    protected $fillable = [
        'custodian_id',
        'student_id',
        'relationship',
        'enabled'
    ];
    
    public function custodian() {
        return $this->belongsTo('App\Models\Custodian');
    }
    
    public function student() {
        return $this->belongsTo('App\Models\Student');
    }
    
    public function datatable() {
        
        $columns = [
            ['db' => 'CustodianStudent.id', 'dt' => 0],
//            ['db' => 'User.realname as studentname', 'dt' => 2],
            ['db' => 'User.realname as custodianname', 'dt' => 1],
            [
                'db' => 'Student.id as studentname', 'dt' => 2,
                'formatter' => function ($d, $row) {
                    $student = Student::whereId($d)->first();
                    return $student->user->realname;
                }
            ],
            ['db' => 'CustodianStudent.relationship', 'dt' => 3],
            ['db' => 'CustodianStudent.created_at', 'dt' => 4],
            ['db' => 'CustodianStudent.updated_at', 'dt' => 5],
            [
                'db' => 'CustodianStudent.enabled', 'dt' => 6,
                'formatter' => function ($d, $row) {
                    return Datatable::dtOps($this, $d, $row);
                }
            ],
        
        ];
        
        $joins = [
            [
                'table' => 'custodians',
                'alias' => 'Custodian',
                'type' => 'INNER',
                'conditions' => [
                    'Custodian.id = CustodianStudent.custodian_id'
                ]
            ],
            [
                'table' => 'users',
                'alias' => 'User',
                'type' => 'INNER',
                'conditions' => [
                    'User.id = Custodian.user_id'
                ]
            ],
            
            [
                'table' => 'students',
                'alias' => 'Student',
                'type' => 'INNER',
                'conditions' => [
                    'Student.id = CustodianStudent.student_id',
                ]
            ],
//            [
//                'table' => 'users',
//                'alias' => 'User',
//                'type' => 'INNER',
//                'conditions' => [
//                    'Student.user_id = User.id'
//                ]
//            ],
        
        ];
        
        return Datatable::simple($this, $columns, $joins);
        
    }
    
}
