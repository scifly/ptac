<?php

namespace App\Models;

use App\Facades\DatatableFacade as Datatable;
use App\Helpers\Constant;
use App\Helpers\Snippet;
use App\Helpers\ModelTrait;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Auth;

/**
 * App\Models\PollQuestionnaireSubject 调查问卷题目
 *
 * @property int $id
 * @property string $subject 题目名称
 * @property int $pq_id 调查问卷ID
 * @property int $subject_type 题目类型：0 - 单选，1 - 多选, 2 - 填空
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|PollQuestionnaireSubject whereCreatedAt($value)
 * @method static Builder|PollQuestionnaireSubject whereId($value)
 * @method static Builder|PollQuestionnaireSubject wherePqId($value)
 * @method static Builder|PollQuestionnaireSubject whereSubject($value)
 * @method static Builder|PollQuestionnaireSubject whereSubjectType($value)
 * @method static Builder|PollQuestionnaireSubject whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read PollQuestionnaireAnswer $pollquestionnaireAnswer
 * @property-read PollQuestionnaire $pollquestionnaire
 * @property-read Collection|PollQuestionnaireSubjectChoice[] $pollquestionnairechoice
 * @property-read PollQuestionnaire $poll_questionnaire
 * @property-read PollQuestionnaireAnswer $poll_questionnaire_answer
 * @property-read Collection|PollQuestionnaireSubjectChoice[] $poll_questionnaire_choice
 * @property-read PollQuestionnaire $pollQuestionnaire
 * @property-read PollQuestionnaireAnswer $pollQuestionnaireAnswer
 * @property-read Collection|PollQuestionnaireSubjectChoice[] $pollQuestionnaireSubjectChoice
 */
class PollQuestionnaireSubject extends Model {

    use ModelTrait;

    protected $table = 'poll_questionnaire_subjects';

    protected $fillable = [
        'subject', 'pq_id', 'subject_type',
        'created_at', 'updated_at'
    ];
    
    const SINGLE = 0;
    const MULTIPLE = 1;
    const FILLBLANK = 2;
    const SUBJECT_TYPES = [
        self::SINGLE => '单选',
        self::MULTIPLE => '多选',
        self::FILLBLANK => '填空'
    ];

    /**
     * @return HasOne
     */
    function pollQuestionnaireAnswer() {
        
        return $this->hasOne('App\Models\PollQuestionnaireAnswer', 'pqs_id', 'id');
        
    }

    /**
     * @return HasMany
     */
    function pollQuestionnaireSubjectChoice() {
        
        return $this->hasMany("App\Models\PollQuestionnaireSubjectChoice", 'pqs_id', 'id');
        
    }

    /**
     * @return BelongsTo
     */
    function pollQuestionnaire() {
        
        return $this->belongsTo('App\Models\PollQuestionnaire', 'pq_id');
        
    }
    
    /**
     * 删除问卷题目
     *
     * @param $id
     * @return bool|null
     * @throws Exception
     */
    function remove($id) {

        $pqSubject = $this->find($id);
        if (!$pqSubject) { return false; }
        
        return $this->removable($pqSubject) ? $pqSubject->delete() : false;

    }
    
    /**
     * 投票问卷问题列表
     *
     * @return array
     */
    function datatable() {

        $columns = [
            ['db' => 'PollQuestionnaireSubject.id', 'dt' => 0],
            ['db' => 'PollQuestionnaireSubject.subject', 'dt' => 1],
            ['db' => 'PollQuestionnaire.name as pq_name', 'dt' => 2],
            [
                'db' => 'PollQuestionnaireSubject.subject_type', 'dt' => 3,
                'formatter' => function ($d) {
                    return self::SUBJECT_TYPES[$d];
                },
            ],
            [
                'db' => 'PollQuestionnaireSubject.id as subject_id', 'dt' => 4,
                'formatter' => function ($d) {
                    $editLink = sprintf(Snippet::DT_LINK_EDIT, 'edit_' . $d);
                    $delLink = sprintf(Snippet::DT_LINK_DEL, $d);
                    return ($this::uris()['edit'] ? $editLink : '')
                        . ($this::uris()['destroy'] ? Snippet::DT_SPACE . $delLink : '');
                },
            ],
        ];
        $joins = [
            [
                'table' => 'poll_questionnaires',
                'alias' => 'PollQuestionnaire',
                'type' => 'left',
                'conditions' => [
                    'PollQuestionnaire.id = PollQuestionnaireSubject.pq_id',
                ],
            ],
            [
                'table' => 'schools',
                'alias' => 'School',
                'type' => 'INNER',
                'conditions' => [
                    'School.id = PollQuestionnaire.school_id',
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
