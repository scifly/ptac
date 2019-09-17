<?php
namespace App\Models;

use App\Facades\Datatable;
use App\Helpers\Constant;
use App\Helpers\ModelTrait;
use Exception;
use Illuminate\Database\Eloquent\{Model,
    Relations\BelongsTo,
    Relations\HasMany};
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use Throwable;

/**
 * App\Models\Poll 调查问卷
 *
 * @property int $id
 * @property int $school_id 所属学校ID
 * @property int $user_id 发起者用户ID
 * @property string $name 问卷调查名称
 * @property string $start 开始时间
 * @property string $end 结束时间
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $enabled
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\PollTopic[] $pollTopics
 * @property-read int|null $poll_topics_count
 * @property-read \App\Models\School $school
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Poll newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Poll newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Poll query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Poll whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Poll whereEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Poll whereEnd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Poll whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Poll whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Poll whereSchoolId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Poll whereStart($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Poll whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Poll whereUserId($value)
 * @mixin \Eloquent
 */
class Poll extends Model {
    
    use ModelTrait;
    
    protected $fillable = [
        'school_id', 'user_id', 'name',
        'start', 'end', 'enabled',
    ];
    
    /** @return BelongsTo */
    function school() { return $this->belongsTo('App\Models\School'); }
    
    /** @return BelongsTo */
    function user() { return $this->belongsTo('App\Models\User'); }
    
    /** @return HasMany */
    function pollTopics() { return $this->hasMany('App\Models\PollTopic'); }
    
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