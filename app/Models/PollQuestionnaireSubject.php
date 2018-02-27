<?php

namespace App\Models;

use App\Facades\DatatableFacade as Datatable;
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
    public function remove($id) {

        $pqs = self::find($id);
        if (!$pqs) { return false; }
        
        return self::removable($pqs) ? $pqs->delete() : false;

    }
    
    /**
     * 投票问卷问题列表
     *
     * @return array
     */
    public function datatable() {

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
                    $showLink = sprintf(Snippet::DT_LINK_SHOW, 'show_' . $d);
                    $editLink = sprintf(Snippet::DT_LINK_EDIT, 'edit_' . $d);
                    $delLink = sprintf(Snippet::DT_LINK_DEL, $d);
                    return $showLink . Snippet::DT_SPACE .
                        $editLink . Snippet::DT_SPACE . $delLink;
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
        // todo: 根据学校和角色进行过滤

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
            case 0: return '单选';
            case 1: return '多选';
            case 2: return '填空';
            default: return '错误';
        }

    }
    
}
