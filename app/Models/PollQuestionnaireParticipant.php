<?php

namespace App\Models;

use Carbon\Carbon;
use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * App\Models\PollQuestionnaireParticipant 调查问卷参与者
 *
 * @property int $id
 * @property int $pq_id 调查问卷ID
 * @property int $user_id 参与者用户ID
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|PollQuestionnaireParticipant whereCreatedAt($value)
 * @method static Builder|PollQuestionnaireParticipant whereId($value)
 * @method static Builder|PollQuestionnaireParticipant wherePqId($value)
 * @method static Builder|PollQuestionnaireParticipant whereUpdatedAt($value)
 * @method static Builder|PollQuestionnaireParticipant whereUserId($value)
 * @mixin Eloquent
 * @property-read PollQuestionnaire $pollquestionnaire
 * @property-read User $user
 */
class PollQuestionnaireParticipant extends Model {

    protected $table = 'poll_questionnaire_participants';

    protected $fillable = ['pq_id', 'user_id', 'created_at', 'updated-at'];

    function pollquestionnaire() { return $this->belongsTo('App\Models\PollQuestionnaire'); }

    function user() { return $this->belongsTo('App\Models\User'); }
    
    /**
     * 保存调查问卷参与者
     *
     * @param array $data
     * @return bool
     */
    function store(array $data) {
        
        return $this->create($data) ? true : false;
        
    }
    
    /**
     * 更新调查问卷参与者
     *
     * @param array $data
     * @param $id
     * @return bool
     */
    function modify(array $data, $id) {
        
        $pqp = $this->find($id);
        if (!$pqp) { return false; }
        
        return $this->update($data);
        
    }
    
    /**
     * 删除调查问卷参与者
     *
     * @param $id
     * @return bool|null
     * @throws Exception
     */
    function remove($id) {
        
        $pqp = $this->find($id);
        if (!$pqp) { return false; }
        
        return $pqp->delete();
        
    }
    
    /**
     * 删除指定调查问卷包含的调查问卷参与者
     *
     * @param $pqId
     * @return bool|null
     * @throws Exception
     */
    function removeByPqId($pqId) {
        
        return $this->where('pq_id', $pqId)->delete();
        
    }
    
    /**
     * 删除指定用户参与的调查问卷记录
     *
     * @param $userId
     * @return bool|null
     * @throws Exception
     */
    function removeByUserId($userId) {
        
        return $this->where('user_id', $userId)->delete();
        
    }
    
}
