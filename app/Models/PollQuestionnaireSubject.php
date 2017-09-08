<?php

namespace App\Models;

use App\Facades\DatatableFacade as Datatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\PollQuestionnaireSubject
 *
 * @property int $id
 * @property string $subject 题目名称
 * @property int $pq_id 调查问卷ID
 * @property int $subject_type 题目类型：0 - 单选，1 - 多选, 2 - 填空
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static Builder|PollQuestionnaireSubject whereCreatedAt($value)
 * @method static Builder|PollQuestionnaireSubject whereId($value)
 * @method static Builder|PollQuestionnaireSubject wherePqId($value)
 * @method static Builder|PollQuestionnaireSubject whereSubject($value)
 * @method static Builder|PollQuestionnaireSubject whereSubjectType($value)
 * @method static Builder|PollQuestionnaireSubject whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read \App\Models\PollQuestionnaireAnswer $pollquestionnaireAnswer
 * @property-read \App\Models\PollQuestionnaire $pollquestionnaire
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\PollQuestionnaireChoice[] $pollquestionnairechoice
 */
class PollQuestionnaireSubject extends Model {
    //
    protected $table = 'poll_questionnaire_subjects';
    
    protected $fillable = ['subject', 'pq_id', 'subject_type', 'created_at', 'updated_at'];
    
    public function pollquestionnaireAnswer() {
        return $this->hasOne('App\Models\PollQuestionnaireAnswer'
            , 'pqs_id', 'id');
    }
    
    public function pollquestionnairechoice() {
        return $this
            ->hasMany("App\Models\PollQuestionnaireChoice"
                , 'pqs_id', 'id');
    }
    
    public function pollquestionnaire() {
        return $this->hasOne('App\Models\PollQuestionnaire'
            , 'pq_id', 'id');
    }

    public function getType($type){
        switch ($type)
        {
            case 0:
                return '单选';
            case 1:
                return '多选';
            case 2:
                return '填空';
            default:
                return '错误';
        }
    }

    public function datatable() {

        $columns = [
            ['db' => 'PollQuestionnaireSubject.id', 'dt' => 0],
            ['db' => 'PollQuestionnaireSubject.subject', 'dt' => 1],
            ['db' => 'PollQuestionnaire.name as pqname', 'dt' => 2],
            [
                'db' => 'PollQuestionnaireSubject.subject_type', 'dt' => 3,
                'formatter' => function ($d) {
                    return $this->getType($d);
                }
            ],
            [
                'db' => 'PollQuestionnaireSubject.id as subject_id', 'dt' => 4,
                'formatter' => function ($d) {
                    $showLink = sprintf(Datatable::DT_LINK_SHOW, 'show_' . $d);
                    $editLink = sprintf(Datatable::DT_LINK_EDIT, 'edit_' . $d);
                    $delLink = sprintf(Datatable::DT_LINK_DEL, $d);
                    return $showLink . Datatable::DT_SPACE .
                        $editLink .  Datatable::DT_SPACE . $delLink ;
                }
            ]
        ];
        $joins = [
            [
                'table' => 'poll_questionnaires',
                'alias' => 'PollQuestionnaire',
                'type' => 'INNER',
                'conditions' => [
                    'PollQuestionnaire.id = PollQuestionnaireSubject.pq_id'
                ]

            ]
        ];

        return Datatable::simple($this, $columns, $joins);
    }
}
