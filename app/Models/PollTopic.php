<?php
namespace App\Models;

use App\Facades\Datatable;
use App\Helpers\{Constant, ModelTrait};
use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\{Builder, Collection, Model, Relations\BelongsTo, Relations\HasMany};
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\{Auth, DB};
use Throwable;

/**
 * App\Models\PollTopic 调查问卷题目
 *
 * @property int $id
 * @property int $poll_id 调查问卷ID
 * @property string $topic 题目名称
 * @property int $category 题目类型：0 - 单选，1 - 多选, 2 - 填空
 * @property mixed $content 题目内容
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $enabled
 * @property-read Poll $poll
 * @property-read Collection|PollReply[] $pollReplies
 * @property-read int|null $poll_replies_count
 * @method static Builder|PollTopic newModelQuery()
 * @method static Builder|PollTopic newQuery()
 * @method static Builder|PollTopic query()
 * @method static Builder|PollTopic whereCategory($value)
 * @method static Builder|PollTopic whereContent($value)
 * @method static Builder|PollTopic whereCreatedAt($value)
 * @method static Builder|PollTopic whereEnabled($value)
 * @method static Builder|PollTopic whereId($value)
 * @method static Builder|PollTopic wherePollId($value)
 * @method static Builder|PollTopic whereTopic($value)
 * @method static Builder|PollTopic whereUpdatedAt($value)
 * @mixin Eloquent
 * @property-read Collection|PollReply[] $replies
 * @property-read int|null $replies_count
 */
class PollTopic extends Model {
    
    use ModelTrait;
    
    protected $fillable = [
        'poll_id', 'topic', 'category',
        'content', 'enabled'
    ];
    
    /** @return BelongsTo */
    function poll() { return $this->belongsTo('App\Models\Poll', 'pq_id'); }
    
    /** @return HasMany */
    function replies() { return $this->hasMany('App\Models\PollReply'); }
    
    /**
     * 投票问卷问题列表
     *
     * @return array
     */
    function index() {
        
        $columns = [
            ['db' => 'PollTopic.id', 'dt' => 0],
            ['db' => 'PollTopic.subject', 'dt' => 1],
            ['db' => 'Poll.name as pq_name', 'dt' => 2],
            [
                'db' => 'PollTopic.category', 'dt' => 3,
                'formatter' => function ($d) {
                    return !$d ? '填空' : ($d == 1 ? '单选' : '多选');
                }
            ],
            ['db' => 'Poll.created_at', 'dt' => 4],
            ['db' => 'Poll.updated_at', 'dt' => 5],
            [
                'db'        => 'PollTopic.enabled', 'dt' => 6,
                'formatter' => function ($d) {
                    return Datatable::status(null, $d, false);
                },
            ],
        ];
        $joins = [
            [
                'table'      => 'pools',
                'alias'      => 'Poll',
                'type'       => 'left',
                'conditions' => [
                    'Poll.id = PollTopic.poll_id',
                ],
            ],
            [
                'table'      => 'schools',
                'alias'      => 'School',
                'type'       => 'INNER',
                'conditions' => [
                    'School.id = Poll.school_id',
                ],
            ],
        ];
        $condition = 'School.id = ' . $this->schoolId();
        $user = Auth::user();
        if (!in_array($user->role(), Constant::SUPER_ROLES)) {
            $condition .= ' AND Poll.user_id = ' . $user->id;
        }
        
        return Datatable::simple(
            $this, $columns, $joins, $condition
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
        
        try {
            DB::transaction(function () use ($id) {
                $this->purge(
                    ['PollTopic', 'PollReply'],
                    'poll_topic_id', 'purge', $id
                );
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
}
