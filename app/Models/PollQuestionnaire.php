<?php
namespace App\Models;

use App\Facades\DatatableFacade as Datatable;
use App\Helpers\ModelTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\PollQuestionnaire 调查问卷
 *
 * @property int $id
 * @property int $school_id 所属学校ID
 * @property int $user_id 发起者用户ID
 * @property string $name 问卷调查名称
 * @property string $start 开始时间
 * @property string $end 结束时间
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property int $enabled
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
 * @property-read PollQuestionnaireAnswer $pollquestionnaireAnswer
 * @property-read PollQuestionnaireParticipant $pollquestionnairePartcipant
 * @property-read School $school
 * @property-read User $user
 */
class PollQuestionnaire extends Model {
    
    use ModelTrait;
    
    protected $table = 'poll_questionnaires';
    
    protected $fillable = ['school_id', 'user_id', 'name', 'start', 'end', 'enabled'];
    
    /**
     * 返回指定调查问卷所属的学校对象
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function school() { return $this->belongsTo('App\Models\School'); }
    
    /**
     * 返回调查问卷发起者的用户对象
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user() { return $this->belongsTo('App\Models\User'); }
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function poll_questionnaire_answer() {
        return $this->hasOne('App\Models\PollQuestionnaireAnswer', 'pq_id');
    }
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function poll_questionnaire_partcipant() {
        return $this->hasOne('App\Models\PollQuestionnaireParticipant', 'pq_id');
    }
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function poll_questionnaire_subject() {
        return $this->hasMany('App\Models\PollQuestionnaireSubject', 'pq_id');
    }
    
    /**
     * 删除问卷
     *
     * @param $id
     * @return bool|null
     */
    public function remove($id) {
        
        $pollQuestionnaire = $this->find($id);
        if (!$pollQuestionnaire) { return false; }
        return $this->removable($pollQuestionnaire) ? $pollQuestionnaire->delete() : false;
        
    }
    
    public function dataTable() {
        
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
                'db'        => 'PollQuestionnaire.enabled', 'dt' => 8,
                'formatter' => function ($d, $row) {
                    return Datatable::dtOps($this, $d, $row);
                },
            ],
        ];
        $joins = [
            [
                'table'      => 'schools',
                'alias'      => 'School',
                'type'       => 'INNER',
                'conditions' => [
                    'School.id = PollQuestionnaire.school_id',
                ],
            ],
            [
                'table'      => 'users',
                'alias'      => 'User',
                'type'       => 'INNER',
                'conditions' => [
                    'User.id = PollQuestionnaire.user_id',
                ],
            ],
        ];
        return Datatable::simple($this, $columns, $joins);
    }
}
