<?php

namespace App\Models;

use App\Facades\DatatableFacade as Datatable;
use App\Helpers\ModelTrait;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

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
 * @property-read PollQuestionnaireAnswer $pollquestionnaireAnswer
 * @property-read PollQuestionnaire $pollquestionnaire
 * @property-read Collection|PollQuestionnaireSubjectChoice[] $pollquestionnairechoice
 * @property-read PollQuestionnaire $poll_questionnaire
 * @property-read PollQuestionnaireAnswer $poll_questionnaire_answer
 * @property-read Collection|PollQuestionnaireSubjectChoice[] $poll_questionnaire_choice
 * @property-read \App\Models\PollQuestionnaire $pollQuestionnaire
 * @property-read \App\Models\PollQuestionnaireAnswer $pollQuestionnaireAnswer
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\PollQuestionnaireSubjectChoice[] $pollQuestionnaireSubjectChoice
 */
class PollQuestionnaireSubject extends Model {

    use ModelTrait;

    protected $table = 'poll_questionnaire_subjects';

    protected $fillable = ['subject', 'pq_id', 'subject_type', 'created_at', 'updated_at'];

    /**
     * @return HasOne
     */
    public function pollQuestionnaireAnswer() {
        
        return $this->hasOne('App\Models\PollQuestionnaireAnswer', 'pqs_id', 'id');
        
    }

    /**
     * @return HasMany
     */
    public function pollQuestionnaireSubjectChoice() {
        
        return $this->hasMany("App\Models\PollQuestionnaireSubjectChoice", 'pqs_id', 'id');
        
    }

    /**
     * @return BelongsTo
     */
    public function pollQuestionnaire() {
        
        return $this->belongsTo('App\Models\PollQuestionnaire', 'pq_id');
        
    }
    
    /**
     * 删除问卷题目
     *
     * @param $id
     * @return bool|null
     * @throws Exception
     */
    static function remove($id) {

        $pqSubject = self::find($id);
        if (!$pqSubject) { return false; }
        
        return self::removable($pqSubject) ? $pqSubject->delete() : false;

    }
    
    /**
     * 投票问卷问题列表
     *
     * @return array
     */
    static function dataTable() {

        $columns = [
            ['db' => 'PollQuestionnaireSubject.id', 'dt' => 0],
            ['db' => 'PollQuestionnaireSubject.subject', 'dt' => 1],
            ['db' => 'PollQuestionnaire.name as pq_name', 'dt' => 2],
            [
                'db' => 'PollQuestionnaireSubject.subject_type', 'dt' => 3,
                'formatter' => function ($d) {
                    return self::subjectType($d);
                },
            ],
            [
                'db' => 'PollQuestionnaireSubject.id as subject_id', 'dt' => 4,
                'formatter' => function ($d) {
                    $showLink = sprintf(Datatable::DT_LINK_SHOW, 'show_' . $d);
                    $editLink = sprintf(Datatable::DT_LINK_EDIT, 'edit_' . $d);
                    $delLink = sprintf(Datatable::DT_LINK_DEL, $d);
                    return $showLink . Datatable::DT_SPACE .
                        $editLink . Datatable::DT_SPACE . $delLink;
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
        ];
        return Datatable::simple(self::getModel(), $columns, $joins);
    }
    
    /**
     * 获取问题类型
     *
     * @param $type
     * @return string
     */
    private static function subjectType($type) {

        switch ($type) {
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
    
}
