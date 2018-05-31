<?php

namespace App\Models;

use App\Facades\DatatableFacade as Datatable;
use App\Helpers\Constant;
use App\Helpers\ModelTrait;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Auth;

/**
 * App\Models\PollQuestionnaire 调查问卷
 *
 * @property int $id
 * @property int $school_id 所属学校ID
 * @property int $user_id 发起者用户ID
 * @property string $name 问卷调查名称
 * @property string $start 开始时间
 * @property string $end 结束时间
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $enabled
 * @property-read PollQuestionnaireAnswer $poll_questionnaire_answer
 * @property-read PollQuestionnaireParticipant $poll_questionnaire_partcipant
 * @property-read Collection|PollQuestionnaireSubject[] $poll_questionnaire_subject
 * @property-read School $school
 * @property-read User $user
 * @method static Builder|PollQuestionnaire whereCreatedAt($value)
 * @method static Builder|PollQuestionnaire whereEnabled($value)
 * @method static Builder|PollQuestionnaire whereEnd($value)
 * @method static Builder|PollQuestionnaire whereId($value)
 * @method static Builder|PollQuestionnaire whereName($value)
 * @method static Builder|PollQuestionnaire whereSchoolId($value)
 * @method static Builder|PollQuestionnaire whereStart($value)
 * @method static Builder|PollQuestionnaire whereUpdatedAt($value)
 * @method static Builder|PollQuestionnaire whereUserId($value)
 * @mixin \Eloquent
 */
class PollQuestionnaire extends Model {

    use ModelTrait;

    protected $table = 'poll_questionnaires';

    protected $fillable = [
        'school_id', 'user_id', 'name',
        'start', 'end', 'enabled'
    ];

    /**
     * 返回指定调查问卷所属的学校对象
     *
     * @return BelongsTo
     */
    function school() { return $this->belongsTo('App\Models\School'); }

    /**
     * 返回调查问卷发起者的用户对象
     *
     * @return BelongsTo
     */
    function user() { return $this->belongsTo('App\Models\User'); }

    /**
     * 返回指定调查问卷包含的调查问卷答案对象
     * 
     * @return HasOne
     */
    function poll_questionnaire_answer() {
        
        return $this->hasOne('App\Models\PollQuestionnaireAnswer', 'pq_id');
        
    }

    /**
     * 
     * 
     * @return HasOne
     */
    function poll_questionnaire_partcipant() {
        
        return $this->hasOne('App\Models\PollQuestionnaireParticipant', 'pq_id');
        
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    function poll_questionnaire_subject() {
        
        return $this->hasMany('App\Models\PollQuestionnaireSubject', 'pq_id');
        
    }
    
    /**
     * 保存调查问卷
     *
     * @param array $data
     * @return bool
     */
    function store(array $data) {
        
        $pq = $this->create($data);
        
        return $pq ? true : false;
        
    }
    
    /**
     * 更新调查问卷
     *
     * @param array $data
     * @param $id
     * @return bool
     */
    function modify(array $data, $id) {
        
        $pq = $this->find($id);
        if (!$pq) { return false; }
        
        return $pq->update($data) ? true : false;
        
    }
    
    /**
     * 删除调查问卷
     *
     * @param $id
     * @return bool|null
     * @throws Exception
     */
    function remove($id) {
        
        $pq = $this->find($id);
        if (!$pq) { return false; }

        return $this->removable($pq) ? $pq->delete() : false;

    }
    
    /**
     * 投票问卷列表
     *
     * @return array
     */
    function datatable() {
        
        $columns = [
            ['db' => 'PollQuestionnaire.id', 'dt' => 0],
            ['db' => 'PollQuestionnaire.name', 'dt' => 1],
            ['db' => 'School.name as school_name', 'dt' => 2],
            ['db' => 'User.realname', 'dt' => 3],
            ['db' => 'PollQuestionnaire.start', 'dt' => 4],
            ['db' => 'PollQuestionnaire.end', 'dt' => 5],
            ['db' => 'PollQuestionnaire.created_at', 'dt' => 6],
            ['db' => 'PollQuestionnaire.updated_at', 'dt' => 7],
            [
                'db' => 'PollQuestionnaire.enabled', 'dt' => 8,
                'formatter' => function ($d, $row) {
                    return Datatable::dtOps($d, $row);
                },
            ],
        ];
        $joins = [
            [
                'table' => 'schools',
                'alias' => 'School',
                'type' => 'INNER',
                'conditions' => [
                    'School.id = PollQuestionnaire.school_id',
                ],
            ],
            [
                'table' => 'users',
                'alias' => 'User',
                'type' => 'INNER',
                'conditions' => [
                    'User.id = PollQuestionnaire.user_id',
                ],
            ],
        ];
        $condition = 'School.id = ' . $this->schoolId();
        $user = Auth::user();
        if (!in_array($user->group->name, Constant::SUPER_ROLES)) {
            $condition .= ' AND PollQuestionnaire.user_id = ' . $user->id;
        }        
        
        return Datatable::simple(
            $this->getModel(), $columns, $joins, $condition
        );
        
    }
    
}
