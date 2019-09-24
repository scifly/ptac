<?php
namespace App\Models;

use App\Facades\Datatable;
use App\Helpers\{Constant, ModelTrait};
use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\{Builder, Collection, Model, Relations\BelongsTo, Relations\HasMany};
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\{Auth, DB, Request};
use Throwable;

/**
 * App\Models\Poll 调查问卷
 *
 * @property int $id
 * @property int $school_id 所属学校ID
 * @property int $user_id 发起者用户ID
 * @property int $message_id 消息ID
 * @property string $name 问卷调查名称
 * @property string $start 开始时间
 * @property string $end 结束时间
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $enabled
 * @property-read Collection|PollTopic[] $pollTopics
 * @property-read int|null $poll_topics_count
 * @property-read School $school
 * @property-read User $user
 * @property-read Message $message
 * @method static Builder|Poll newModelQuery()
 * @method static Builder|Poll newQuery()
 * @method static Builder|Poll query()
 * @method static Builder|Poll whereCreatedAt($value)
 * @method static Builder|Poll whereEnabled($value)
 * @method static Builder|Poll whereEnd($value)
 * @method static Builder|Poll whereId($value)
 * @method static Builder|Poll whereMessageId($value)
 * @method static Builder|Poll whereName($value)
 * @method static Builder|Poll whereSchoolId($value)
 * @method static Builder|Poll whereStart($value)
 * @method static Builder|Poll whereUpdatedAt($value)
 * @method static Builder|Poll whereUserId($value)
 * @mixin Eloquent
 * @property-read Collection|PollTopic[] $topics
 * @property-read int|null $topics_count
 */
class Poll extends Model {
    
    use ModelTrait;
    
    protected $fillable = [
        'school_id', 'user_id', 'message_id',
        'name', 'start', 'end', 'enabled',
    ];
    
    /** @return BelongsTo */
    function school() { return $this->belongsTo('App\Models\School'); }
    
    /** @return BelongsTo */
    function user() { return $this->belongsTo('App\Models\User'); }
    
    /** @return BelongsTo */
    function message() { return $this->belongsTo('App\Models\Message'); }
    
    /** @return HasMany */
    function topics() { return $this->hasMany('App\Models\PollTopic'); }
    
    /**
     * 投票问卷列表
     *
     * @return array
     */
    function index() {
        
        $columns = [
            ['db' => 'Poll.id', 'dt' => 0],
            ['db' => 'Poll.name', 'dt' => 1],
            [
                'db'        => 'School.name as sname', 'dt' => 2,
                'formatter' => function ($d) {
                    return $this->iconHtml($d, 'school');
                },
            ],
            ['db' => 'User.realname', 'dt' => 3],
            ['db' => 'Poll.start', 'dt' => 4],
            ['db' => 'Poll.end', 'dt' => 5],
            ['db' => 'Poll.created_at', 'dt' => 6],
            ['db' => 'Poll.updated_at', 'dt' => 7],
            [
                'db'        => 'Poll.enabled', 'dt' => 8,
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
                    'School.id = Poll.school_id',
                ],
            ],
            [
                'table'      => 'users',
                'alias'      => 'User',
                'type'       => 'INNER',
                'conditions' => [
                    'User.id = Poll.user_id',
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
                $pqsIds = PollTopic::whereIn('pq_id', $ids)
                    ->pluck('id')->toArray();
                Request::replace(['ids' => $pqsIds]);
                (new PollTopic)->remove();
                Request::replace(['ids' => $ids]);
                $class = 'Poll';
                $this->purge([$class, $class . 'Participant', $class . 'Answer'], 'pq_id');
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
}