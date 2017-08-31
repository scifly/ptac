<?php

namespace App\Models;

use App\Facades\DatatableFacade as Datatable;
use App\Http\Requests\EducatorRequest;
use App\Models\EducatorClass;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Educator
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
 */
class Educator extends Model {
    
    protected $fillable = [
        'user_id',
        'team_ids',
        'school_id',
        'sms_quote',
        'enabled',
    ];
    
    public function user() { return $this->belongsTo('App\Models\User'); }
    
    public function school() { return $this->belongsTo('App\Models\School'); }
    
    public function classes() {
        
        return $this->belongsToMany(
            'App\Models\Squad',
            'educators_classes',
            'class_id',
            'educator_id'
        );
        
    }
    
    /**
     * 根据SchoolIds返回教职员工列表
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
    
    public function existed(EducatorRequest $request, $id = NULL) {

        if (!$id) {
            $educator = $this->where([
                'school_id' => $request->input('school_id'),
                'user_id' => $request->input('user_id')
            ])->first();
        } else {
            $educator = $this->where('school_id', $request->input('school_id'))
                ->where('id', '<>', $id)
                ->where('user_id', $request->input('user_id'))
                ->first();
        }
        return $educator ? true : false;

    }

    public function educatorClass() { return $this->hasOne('App\Models\EducatorClass'); }
    
    public function datatable() {
        
        $columns = [
            ['db' => 'Educator.id', 'dt' => 0],
            ['db' => 'User.username', 'dt' => 1],
            ['db' => 'Educator.team_ids', 'dt' => 2],
            ['db' => 'Shool.name', 'dt' => 3],
            ['db' => 'Educator.sms_quote', 'dt' => 4],
            ['db' => 'Educator.created_at', 'dt' => 5],
            ['db' => 'Educator.updated_at', 'dt' => 6],
            [
                'db' => 'Educator.enabled', 'dt' => 7,
                'formatter' => function ($d, $row) {
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
                    'User.id = Educator.user_id'
                ]
            ],
            [
                'table' => 'schools',
                'alias' => 'Shool',
                'type' => 'INNER',
                'conditions' => [
                    'Shool.id = Educator.school_id'
                ]
            ]
        ];
        
        return Datatable::simple($this, $columns, $joins);
    }
    
}

