<?php

namespace App\Models;

use App\Facades\DatatableFacade as Datatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\PollQuestionnaireChoice
 *
 * @mixin \Eloquent
 * @property int $id
 * @property int $pqs_id 题目ID
 * @property string $choice 选项内容
 * @property int $seq_no 选项排序编号
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\Models\PollQuestionnaireSubject $pollquestionnaireSubject
 * @method static Builder|PollQuestionnaireChoice whereChoice($value)
 * @method static Builder|PollQuestionnaireChoice whereCreatedAt($value)
 * @method static Builder|PollQuestionnaireChoice whereId($value)
 * @method static Builder|PollQuestionnaireChoice wherePqsId($value)
 * @method static Builder|PollQuestionnaireChoice whereSeqNo($value)
 * @method static Builder|PollQuestionnaireChoice whereUpdatedAt($value)
 */
class PollQuestionnaireChoice extends Model {
    //
    protected $table = 'poll_questionnaire_subject_choices';
    
    protected $fillable = ['pqs_id', 'choice', 'seq_no', 'created_at', 'updated_at'];
    
    public function pollquestionnaireSubject() {
        return $this->belongsTo('App\Models\PollQuestionnaireSubject'
            , 'pqs_id'
            , 'id');
    }
    
    public function datatable() {
        
        $columns = [
            ['db' => 'PollQuestionnaireChoice.id', 'dt' => 0],
            ['db' => 'PqSubject.subject', 'dt' => 1],
            ['db' => 'PollQuestionnaireChoice.choice', 'dt' => 2],
            ['db' => 'PollQuestionnaireChoice.seq_no', 'dt' => 3],
            [
                'db' => 'PollQuestionnaireChoice.id as choice_id', 'dt' => 4,
                'formatter' => function ($d) {
                    $showLink = sprintf(Datatable::DT_LINK_SHOW, 'show_' . $d);
                    $editLink = sprintf(Datatable::DT_LINK_EDIT, 'edit_' . $d);
                    $delLink = sprintf(Datatable::DT_LINK_DEL, $d);
                    return $showLink . Datatable::DT_SPACE .
                        $editLink . Datatable::DT_SPACE . $delLink;
                }
            ]
        ];
        $joins = [
            [
                'table' => 'poll_questionnaire_subjects',
                'alias' => 'PqSubject',
                'type' => 'INNER',
                'conditions' => [
                    'PqSubject.id = PollQuestionnaireChoice.pqs_id'
                ]
            
            ]
        ];
        
        return Datatable::simple($this, $columns, $joins);
    }
}
