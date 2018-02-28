<?php

namespace App\Models;

use App\Facades\DatatableFacade as Datatable;
use App\Helpers\Snippet;
use Carbon\Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


/**
 * App\Models\PollQuestionnaireSubjectChoice 调查问卷题目选项
 *
 * @property int $id
 * @property int $pqs_id 题目ID
 * @property string $choice 选项内容
 * @property int $seq_no 选项排序编号
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read PollQuestionnaireSubject $pollquestionnaireSubject
 * @method static Builder|PollQuestionnaireSubjectChoice whereChoice($value)
 * @method static Builder|PollQuestionnaireSubjectChoice whereCreatedAt($value)
 * @method static Builder|PollQuestionnaireSubjectChoice whereId($value)
 * @method static Builder|PollQuestionnaireSubjectChoice wherePqsId($value)
 * @method static Builder|PollQuestionnaireSubjectChoice whereSeqNo($value)
 * @method static Builder|PollQuestionnaireSubjectChoice whereUpdatedAt($value)
 * @mixin Eloquent
 */
class PollQuestionnaireSubjectChoice extends Model {

    protected $table = 'poll_questionnaire_subject_choices';

    protected $fillable = ['pqs_id', 'choice', 'seq_no', 'created_at', 'updated_at'];
    
    /**
     * @return BelongsTo
     */
    public function pollquestionnaireSubject() {
        
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
            [
                'db' => 'PollQuestionnaireChoice.id as choice_id', 'dt' => 4,
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
                'table' => 'poll_questionnaire_subjects',
                'alias' => 'PqSubject',
                'type' => 'INNER',
                'conditions' => [
                    'PqSubject.id = PollQuestionnaireChoice.pqs_id',
                ],
            ],
        ];
        // todo: 增加过滤条件
        return Datatable::simple(self::getModel(), $columns, $joins);
        
    }
    
}
