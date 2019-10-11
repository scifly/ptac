<?php
namespace App\Models;

use App\Facades\Datatable;
use App\Helpers\{Constant, ModelTrait};
use Eloquent;
use Form;
use Html;
use Illuminate\Database\Eloquent\{Builder, Collection, Model, Relations\BelongsTo, Relations\HasMany};
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\{Auth};
use Request;
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
 * @property-read Collection|PollReply[] $replies
 * @property-read int|null $replies_count
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
 */
class PollTopic extends Model {
    
    use ModelTrait;
    
    protected $fillable = [
        'poll_id', 'topic', 'category',
        'content', 'enabled',
    ];
    const CATEGORIES = ['填空', '单选', '多选'];
    
    /** @return BelongsTo */
    function poll() { return $this->belongsTo('App\Models\Poll'); }
    
    /** @return HasMany */
    function replies() { return $this->hasMany('App\Models\PollReply', 'poll_topic_id'); }
    
    /**
     * 投票问卷问题列表
     *
     * @return array
     */
    function index() {
        
        $columns = [
            ['db' => 'PollTopic.id', 'dt' => 0],
            ['db' => 'PollTopic.topic', 'dt' => 1],
            ['db' => 'Poll.name', 'dt' => 2],
            [
                'db'        => 'PollTopic.category', 'dt' => 3,
                'formatter' => function ($d) {
                    return self::CATEGORIES[$d];
                },
            ],
            ['db' => 'PollTopic.created_at', 'dt' => 4, 'dr' => true],
            ['db' => 'PollTopic.updated_at', 'dt' => 5, 'dr' => true],
            [
                'db'        => 'PollTopic.enabled', 'dt' => 6,
                'formatter' => function ($d, $row) {
                    return Datatable::status($d, $row, false);
                },
            ],
        ];
        $joins = [
            [
                'table'      => 'polls',
                'alias'      => 'Poll',
                'type'       => 'INNER',
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
     * @throws Throwable
     */
    function modify(array $data, $id) {
        
        return $this->revise(
            $this, $data, $id, null
        );
        
    }
    
    /**
     * 删除问卷题目
     *
     * @param $id
     * @return bool|null
     * @throws Throwable
     */
    function remove($id = null) {
        
        return $this->purge($id, [
            'purge.poll_topic_id' => ['PollReply']
        ]);
        
    }
    
    /** @return array */
    function compose() {
        
        $action = explode('/', Request::path())[1];
        if ($action == 'index') {
            $nil = collect([null => '全部']);
            
            return [
                'titles' => [
                    '#', '名称', '所属问卷',
                    [
                        'title' => '类型',
                        'html'  => $this->htmlSelect(
                            $nil->union(self::CATEGORIES),
                            'filter_category'
                        ),
                    ],
                    ['title' => '创建于', 'html' => $this->htmlDTRange('创建于')],
                    ['title' => '更新于', 'html' => $this->htmlDTRange('更新于')],
                    [
                        'title' => '状态 . 操作',
                        'html'  => $this->htmlSelect(
                            $nil->union(['已启用', '已禁用']), 'filter_enabled'),
                    ],
                ],
                'batch'  => true,
                'filter' => true,
            ];
        } else {
            $topic = $this->find(Request::route('id'));
            
            return [
                'polls'      => Poll::where(['user_id' => Auth::id()])->pluck('name', 'id'),
                'categories' => collect(self::CATEGORIES),
                'options'    => $topic ? $this->options($topic->content) : null,
            ];
        }
        
    }
    
    /**
     * @param string|null $content
     * @return string
     */
    function options($content = null) {
        
        $options = json_decode($content, true) ?? [];
        $html = '';
        $del = Form::button(
            Html::tag('i', '', ['class' => 'fa fa-minus text-blue']),
            ['class' => 'btn btn-box-tool remove-option', 'title' => '移除']
        );
        foreach ($options as $option) {
            $input = Form::text('option[]', $option, [
                'class' => 'form-control text-blue',
            ])->toHtml();
            $tr = join(
                array_map(
                    function ($td) {
                        return Html::tag('td', $td);
                    }, [$input, $del]
                )
            );
            $html .= Html::tag('tr', $tr);
        }
        
        return $html;
        
    }
    
}
