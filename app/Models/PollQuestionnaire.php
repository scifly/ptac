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
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use Throwable;

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
 * @property-read Collection|PollQuestionnaireAnswer[] $pqAnswers
 * @property-read Collection|PollQuestionnaireSubjectChoice[] $pqChoices
 * @property-read Collection|PollQuestionnaireParticipant[] $pqParticipants
 * @property-read Collection|PollQuestionnaireSubject[] $pqSubjects
 * @method static Builder|PollQuestionnaire whereCreatedAt($value)
 * @method static Builder|PollQuestionnaire whereEnabled($value)
 * @method static Builder|PollQuestionnaire whereEnd($value)
 * @method static Builder|PollQuestionnaire whereId($value)
 * @method static Builder|PollQuestionnaire whereName($value)
 * @method static Builder|PollQuestionnaire whereSchoolId($value)
 * @method static Builder|PollQuestionnaire whereStart($value)
 * @method static Builder|PollQuestionnaire whereUpdatedAt($value)
 * @method static Builder|PollQuestionnaire whereUserId($value)
 * @method static Builder|PollQuestionnaire newModelQuery()
 * @method static Builder|PollQuestionnaire newQuery()
 * @method static Builder|PollQuestionnaire query()
 * @mixin Eloquent
 */
class PollQuestionnaire extends Model {
    
    use ModelTrait;
    
    protected $table = 'poll_questionnaires';
    
    protected $fillable = [
        'school_id', 'user_id', 'name',
        'start', 'end', 'enabled',
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
     * 返回指定调查问卷包含的题目对象
     *
     * @return HasMany
     */
    function pqSubjects() {
        
        return $this->hasMany('App\Models\PollQuestionnaireSubject', 'pq_id');
        
    }
    
    /**
     * 返回指定调查问卷对象包含的参与者
     *
     * @return HasMany
     */
    function pqParticipants() {
        
        return $this->hasMany('App\Models\PollQuestionnaireParticipant', 'pq_id');
        
    }
    
    /**
     * 返回指定调查问卷包含的调查问卷答案对象
     *
     * @return HasMany
     */
    function pqAnswers() {
        
        return $this->hasMany('App\Models\PollQuestionnaireAnswer', 'pq_id');
        
    }
    
    /**
     * 返回指定调查问卷对象包含的调查问卷题目选项对象
     *
     * @return HasManyThrough
     */
    function pqChoices() {
        
        return $this->hasManyThrough(
            'App\Models\PollQuestionnaireSubjectChoice',
            'App\Models\PollQuestionnaireSubject',
            'pq_id',
            'pqs_id'
        );
        
    }
    
    /**
     * 投票问卷列表
     *
     * @return array
     */
    function index() {
        
        $columns = [
            ['db' => 'PollQuestionnaire.id', 'dt' => 0],
            ['db' => 'PollQuestionnaire.name', 'dt' => 1],
            [
                'db'        => 'School.name as school_name', 'dt' => 2,
                'formatter' => function ($d) {
                    return Snippet::school($d);
                }
            ],
            ['db' => 'User.realname', 'dt' => 3],
            ['db' => 'PollQuestionnaire.start', 'dt' => 4],
            ['db' => 'PollQuestionnaire.end', 'dt' => 5],
            ['db' => 'PollQuestionnaire.created_at', 'dt' => 6],
            ['db' => 'PollQuestionnaire.updated_at', 'dt' => 7],
            [
                'db'        => 'PollQuestionnaire.enabled', 'dt' => 8,
                'formatter' => function ($d, $row) {
                    return Datatable::status($d, $row, false);
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
        $condition = 'School.id = ' . $this->schoolId();
        $user = Auth::user();
        if (!in_array($user->role(), Constant::SUPER_ROLES)) {
            $condition .= ' AND PollQuestionnaire.user_id = ' . $user->id;
        }
        
        return Datatable::simple(
            $this->getModel(), $columns, $joins, $condition
        );
        
    }
    
    /**
     * 保存调查问卷
     *
     * @param array $data
     * @return bool
     */
    function store(array $data) {
        
        return $this->create($data) ? true : false;
        
    }
    
    /**
     * 更新调查问卷
     *
     * @param array $data
     * @param $id
     * @return bool
     */
    function modify(array $data, $id) {
        
        return $this->find($id)->update($data);
        
    }
    
    /**
     * 删除调查问卷
     *
     * @param $id
     * @return bool|null
     * @throws Throwable
     */
    function remove($id = null) {
        
        try {
            DB::transaction(function () use ($id) {
                $ids = $id ? [$id] : array_values(Request::input('ids'));
                $pqsIds = PollQuestionnaireSubject::whereIn('pq_id', $ids)
                    ->pluck('id')->toArray();
                Request::replace(['ids' => $pqsIds]);
                (new PollQuestionnaireSubject)->remove();
                Request::replace(['ids' => $ids]);
                $class = 'PollQuestionnaire';
                $this->purge([$class, $class . 'Participant', $class . 'Answer'], 'pq_id');
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
}