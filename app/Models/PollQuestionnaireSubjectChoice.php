<?php

namespace App\Models;

use App\Facades\DatatableFacade as Datatable;
use App\Helpers\Constant;
use App\Helpers\ModelTrait;
use App\Helpers\Snippet;
use Carbon\Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

/**
 * App\Models\PollQuestionnaireSubjectChoice 调查问卷题目选项
 *
 * @property int $id
 * @property int $pqs_id 题目ID
 * @property string $choice 选项内容
 * @property int $seq_no 选项排序编号
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read PollQuestionnaireSubject $pollQuestionnaireSubject
 * @method static Builder|PollQuestionnaireSubjectChoice whereChoice($value)
 * @method static Builder|PollQuestionnaireSubjectChoice whereCreatedAt($value)
 * @method static Builder|PollQuestionnaireSubjectChoice whereId($value)
 * @method static Builder|PollQuestionnaireSubjectChoice wherePqsId($value)
 * @method static Builder|PollQuestionnaireSubjectChoice whereSeqNo($value)
 * @method static Builder|PollQuestionnaireSubjectChoice whereUpdatedAt($value)
 * @mixin Eloquent
 */
class PollQuestionnaireSubjectChoice extends Model {
    
    use ModelTrait;

    protected $table = 'poll_questionnaire_subject_choices';

    protected $fillable = [
        'pqs_id', 'choice', 'seq_no', 
    ];
    
    /**
     * @return BelongsTo
     */
    function pollQuestionnaireSubject() {
        
        return $this->belongsTo('App\Models\PollQuestionnaireSubject', 'pqs_id', 'id');
        
    }
    
    /**
     * 投票问卷问题选项列表
     *
     * @return array
     */
    function datatable() {
        
        $columns = [
            ['db' => 'PollQuestionnaireChoice.id', 'dt' => 0],
            ['db' => 'PqSubject.subject', 'dt' => 1],
            ['db' => 'PollQuestionnaireChoice.choice', 'dt' => 2],
            ['db' => 'PollQuestionnaireChoice.seq_no', 'dt' => 3],
            ['db' => 'PollQuestionnaireChoice.created_at', 'dt' => 4],
            ['db' => 'PollQuestionnaireChoice.updated_at', 'dt' => 5],
            [
                'db' => 'PollQuestionnaireChoice.id as choice_id', 'dt' => 6,
                'formatter' => function ($d) {
                    $editLink = sprintf(Snippet::DT_LINK_EDIT, 'edit_' . $d);
                    $delLink = sprintf(Snippet::DT_LINK_DEL, $d);
    
                    return ($this::uris()['edit'] ? $editLink : '')
                        . ($this::uris()['destroy'] ? $delLink : '');
                },
            ],
        ];
        $joins = [
            [
                'table' => 'poll_questionnaire_subjects',
                'alias' => 'PqSubject',
                'type' => 'INNER',
                'conditions' => [
                    'PqSubject.id = PollQuestionnaireChoice.pqs_id',
                ],
            ],
            [
                'table' => 'poll_questionnaires',
                'alias' => 'PollQuestionnaire',
                'type' => 'INNER',
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
            ]
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
