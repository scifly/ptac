<?php
namespace App\Models;

use App\Facades\Datatable;
use App\Helpers\Constant;
use App\Helpers\ModelTrait;
use App\Helpers\Snippet;
use Carbon\Carbon;
use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Throwable;

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
 * @mixin Eloquent
 * @property-read PollQuestionnaireAnswer $pollquestionnaireAnswer
 * @property-read PollQuestionnaire $pollquestionnaire
 * @property-read Collection|PollQuestionnaireSubjectChoice[] $pollquestionnairechoice
 * @property-read PollQuestionnaire $poll_questionnaire
 * @property-read PollQuestionnaireAnswer $poll_questionnaire_answer
 * @property-read Collection|PollQuestionnaireSubjectChoice[] $poll_questionnaire_choice
 * @property-read PollQuestionnaire $pollQuestionnaire
 * @property-read PollQuestionnaireAnswer $pollQuestionnaireAnswer
 * @property-read Collection|PollQuestionnaireSubjectChoice[] $pollQuestionnaireSubjectChoices
 * @property-read \App\Models\PollQuestionnaireAnswer $pqAnswer
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\PollQuestionnaireSubjectChoice[] $pqsChoices
 */
class PollQuestionnaireSubject extends Model {
    
    use ModelTrait;
    
    const SUBJECT_TYPES = [
        0 => '单选',
        1 => '多选',
        2 => '填空',
    ];
    protected $table = 'poll_questionnaire_subjects';
    protected $fillable = [
        'subject', 'pq_id', 'subject_type',
        'created_at', 'updated_at',
    ];
    
    /**
     * 返回指定调查问卷题目对应的答案对象
     *
     * @return HasOne
     */
    function pqAnswer() {
        
        return $this->hasOne('App\Models\PollQuestionnaireAnswer', 'pqs_id', 'id');
        
    }
    
    /**
     * 返回指定调查问卷题目对应的选项
     *
     * @return HasMany
     */
    function pqsChoices() {
        
        return $this->hasMany("App\Models\PollQuestionnaireSubjectChoice", 'pqs_id', 'id');
        
    }
    
    /**
     * 返回指定调查问卷题目对应的调查问卷对象
     *
     * @return BelongsTo
     */
    function pollQuestionnaire() {
        
        return $this->belongsTo('App\Models\PollQuestionnaire', 'pq_id');
        
    }
    
    /**
     * 投票问卷问题列表
     *
     * @return array
     */
    function index() {
        
        $columns = [
            ['db' => 'PollQuestionnaireSubject.id', 'dt' => 0],
            ['db' => 'PollQuestionnaireSubject.subject', 'dt' => 1],
            ['db' => 'PollQuestionnaire.name as pq_name', 'dt' => 2],
            [
                'db'        => 'PollQuestionnaireSubject.subject_type', 'dt' => 3,
                'formatter' => function ($d) {
                    return self::SUBJECT_TYPES[$d];
                },
            ],
            ['db' => 'PollQuestionnaire.created_at', 'dt' => 4],
            ['db' => 'PollQuestionnaire.updated_at', 'dt' => 5],
            [
                'db'        => 'PollQuestionnaireSubject.id as subject_id', 'dt' => 6,
                'formatter' => function ($d) {
                    $editLink = sprintf(Snippet::DT_LINK_EDIT, 'edit_' . $d);
                    $delLink = sprintf(Snippet::DT_LINK_DEL, $d);
                    
                    return (self::uris()['edit'] ? $editLink : '')
                        . (self::uris()['destroy'] ? $delLink : '');
                },
            ],
        ];
        $joins = [
            [
                'table'      => 'poll_questionnaires',
                'alias'      => 'PollQuestionnaire',
                'type'       => 'left',
                'conditions' => [
                    'PollQuestionnaire.id = PollQuestionnaireSubject.pq_id',
                ],
            ],
            [
                'table'      => 'schools',
                'alias'      => 'School',
                'type'       => 'INNER',
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
    
    /**
     * 保存调查问卷题目
     *
     * @param array $data
     * @return bool
     */
    function store(array $data) {
        
        return $this->create($data) ? true : false;
        
    }
    
    /**
     * 更新调查问卷题目
     *
     * @param array $data
     * @param $id
     * @return bool
     */
    function modify(array $data, $id) {
        
        return $this->find($id)->update($data);
        
    }
    
    /**
     * 删除问卷题目
     *
     * @param $id
     * @return bool|null
     * @throws Throwable
     */
    function remove($id = null) {
        
        return $this->del($this, $id);
        
    }
    
    /**
     * 删除指定调查问卷题目的所有相关数据
     *
     * @param $id
     * @return bool
     * @throws Throwable
     */
    function purge($id) {
        
        try {
            DB::transaction(function () use ($id) {
                PollQuestionnaireAnswer::wherePqsId($id)->delete();
                PollQuestionnaireSubjectChoice::wherePqsId($id)->delete();
                $this->find($id)->delete();
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
}
