<?php

namespace App\Models;

use App\Facades\DatatableFacade as Datatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use App\Http\Requests\EducatorClassRequest;

/**
 * App\Models\EducatorClass
 *
 * @property int $id
 * @property int $educator_id 教职员工ID
 * @property int $class_id 班级ID
 * @property int $subject_id 科目ID
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static Builder|EducatorClass whereClassId($value)
 * @method static Builder|EducatorClass whereCreatedAt($value)
 * @method static Builder|EducatorClass whereEducatorId($value)
 * @method static Builder|EducatorClass whereId($value)
 * @method static Builder|EducatorClass whereSubjectId($value)
 * @method static Builder|EducatorClass whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property int $enabled 是否启用
 * @property-read \App\Models\Educator $educator
 * @property-read \App\Models\Squad $squad
 * @property-read \App\Models\Subject $subject
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\EducatorClass whereEnabled($value)
 */
class EducatorClass extends Model {
    
    protected $table = 'educators_classes';
    
    protected $fillable = [
        'educator_id',
        'class_id',
        'subject_id',
        'enabled'
    ];
    
    public function educator() {
        return $this->belongsTo('App\Models\Educator');
    }
    
    public function subject() {
        return $this->belongsTo('App\Models\Subject');
    }
    
    public function squad() {
        return $this->belongsTo('App\Models\Squad', 'class_id', 'id');
    }

    
    public function datatable() {
        $columns = [
            ['db' => 'EducatorClass.id', 'dt' => 0],
            ['db' => 'User.realname as usersname', 'dt' => 1],
            ['db' => 'Squad.name as squadname', 'dt' => 2],
            ['db' => 'Subject.name as subjectname', 'dt' => 3],
            ['db' => 'EducatorClass.created_at', 'dt' => 4],
            ['db' => 'EducatorClass.updated_at', 'dt' => 5],
            [
                'db' => 'EducatorClass.enabled', 'dt' => 6,
                'formatter' => function ($d, $row) {
                    return Datatable::dtOps($this, $d, $row);
                }
            ]
        ];
        $joins = [
            [
                'table' => 'classes',
                'alias' => 'Squad',
                'type' => 'INNER',
                'conditions' => [
                    'Squad.id = EducatorClass.class_id'
                ]
            ],
            [
                'table' => 'subjects',
                'alias' => 'Subject',
                'type' => 'INNER',
                'conditions' => [
                    'Subject.id = EducatorClass.subject_id'
                ]
            ],
            
            [
                'table' => 'educators',
                'alias' => 'Educator',
                'type' => 'INNER',
                'conditions' => [
                    'Educator.id = EducatorClass.educator_id'
                ]
            ],
            [
                'table' => 'users',
                'alias' => 'User',
                'type' => 'INNER',
                'conditions' => [
                    'User.id = Educator.user_id'
                ]
            ]
        ];
        
        return Datatable::simple($this, $columns, $joins);
    }
    
    
}
