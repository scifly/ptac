<?php
namespace App\Models;

use App\Facades\Datatable;
use App\Helpers\{Constant, ModelTrait};
use Carbon\Carbon;
use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\{Builder, Collection, Model, Relations\BelongsTo, Relations\BelongsToMany};
use Illuminate\Support\Collection as SCollection;
use Illuminate\Support\Facades\{Auth, DB, Request};
use Throwable;

/**
 * App\Models\Tag 标签
 *
 * @property int $id
 * @property string $name 标签名称
 * @property int $school_id 所属学校ID
 * @property int $user_id 创建者的用户id
 * @property string|null $remark 备注
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $enabled
 * @property-read Collection|User[] $users
 * @property-read School $school
 * @property-read User $creator
 * @property-read Collection|Department[] $departments
 * @property-read int|null $departments_count
 * @property-read int|null $users_count
 * @property-read Collection|Department[] $depts
 * @property-read int|null $depts_count
 * @method static Builder|Tag whereCreatedAt($value)
 * @method static Builder|Tag whereEnabled($value)
 * @method static Builder|Tag whereId($value)
 * @method static Builder|Tag whereName($value)
 * @method static Builder|Tag whereRemark($value)
 * @method static Builder|Tag whereSchoolId($value)
 * @method static Builder|Tag whereUserId($value)
 * @method static Builder|Tag whereUpdatedAt($value)
 * @method static Builder|Tag newModelQuery()
 * @method static Builder|Tag newQuery()
 * @method static Builder|Tag query()
 * @mixin Eloquent
 */
class Tag extends Model {
    
    use ModelTrait;
    
    protected $fillable = [
        'name', 'school_id', 'user_id', 'remark', 'enabled',
    ];
    
    /** @return BelongsTo */
    function school() { return $this->belongsTo('App\Models\School'); }
    
    /** @return BelongsTo */
    function creator() { return $this->belongsTo('App\Models\User'); }
    
    /** @return BelongsToMany */
    function users() { return $this->belongsToMany('App\Models\User', 'tag_user'); }
    
    /** @return BelongsToMany */
    function depts() { return $this->belongsToMany('App\Models\Department', 'department_tag'); }
    
    /**
     * 标签列表
     *
     * @return array
     */
    function index() {
        
        $columns = [
            ['db' => 'Tag.id', 'dt' => 0],
            ['db' => 'Tag.name', 'dt' => 1],
            ['db' => 'Tag.remark', 'dt' => 2],
            ['db' => 'Tag.created_at', 'dt' => 3],
            ['db' => 'Tag.updated_at', 'dt' => 4],
            [
                'db'        => 'Tag.enabled', 'dt' => 5,
                'formatter' => function ($d, $row) {
                    return Datatable::status($d, $row, false);
                },
            ],
        ];
        $condition = 'Tag.school_id = ' . $this->schoolId();
        # 非超级用户(运营、企业、学校)只能查看编辑自己创建的标签
        if (!in_array(Auth::user()->role(), Constant::SUPER_ROLES)) {
            $condition .= ' AND Tag.user_id = ' . Auth::id();
        }
        
        return Datatable::simple(
            $this, $columns, null, $condition
        );
        
    }
    
    /**
     * 保存标签
     *
     * @param array $data
     * @return bool
     * @throws Throwable
     */
    function store(array $data) {
        
        try {
            DB::transaction(function () use ($data) {
                $tag = $this->create($data);
                !isset($data['user_ids']) ?: $this->retain(
                    'TagUser', $tag->id, $data['user_ids']
                );
                !isset($data['dept_ids']) ?: $this->retain(
                    'DepartmentTag', $tag->id, $data['dept_ids'], false
                );
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
    /**
     * 更新标签
     *
     * @param array|null $data
     * @param integer|null $id
     * @return bool
     * @throws Throwable
     */
    function modify(array $data = null, $id = null) {
        
        return $this->revise(
            $this, $data, $id,
            function (Tag $tag) use ($data, $id) {
                TagUser::whereTagId($id)->delete();
                DepartmentTag::whereTagId($id)->delete();
                !isset($data['user_ids']) ?: $this->retain(
                    'TagUser', $id, $data['user_ids']
                );
                !isset($data['dept_ids']) ?: $tag->retain(
                    'DepartmentTag', $id, $data['dept_ids'], false
                );
            }
        );

    }
    
    /**
     * 删除标签
     *
     * @param null $id
     * @return bool|null
     * @throws Throwable
     */
    function remove($id = null) {
        
        return $this->purge($id, [
            'purge.tag_id' => ['DepartmentTag', 'TagUser'],
        ]);
        
    }
    
    /**
     * @param null $action
     * @param Model|null $model
     * @return array
     * @throws Exception
     */
    function compose($action = null, Model $model = null) {
        
        $action = $action ?? explode('/', Request::path())[1];
        switch ($action) {
            case 'index':
                $data = [
                    'titles' => ['#', '名称', '备注', '创建于', '更新于', '状态 . 操作'],
                ];
                break;
            case 'user':
            case 'department':
            case 'message':
                if ($model) {
                    if ($action != 'message') {
                        $builder = $model->{'tags'};
                    } else {
                        $totag = explode(',', json_decode($model->{'content'}, true)['totag']);
                        $builder = $this->whereIn('tag_id', $totag);
                    }
                    $selectedTags = $builder->pluck('name', 'id');
                }
                $data = [
                    'tags'         => $this->list(),
                    'selectedTags' => $selectedTags ?? null,
                ];
                break;
            default:
                $tag = Tag::find(Request::route('id'));
                $departments = $tag ? $tag->depts : null;
                $users = $tag ? $tag->users : null;
                $targetIds = $departments ? $departments->pluck('id') : null;
                $data = [
                    'targets'   => $users ? (new Message)->targetsHtml($tag->users, $targetIds) : null,
                    'targetIds' => isset($targetIds) ? $targetIds->join(',') : '',
                ];
                break;
        }
        
        return $data;
        
    }
    
    /**
     * 返回对当前登录用户可见的标签列表
     *
     * @return SCollection
     */
    function list() {
        
        return $this->where('school_id', $this->schoolId())->get()->when(
            !in_array(Auth::user()->role(), Constant::SUPER_ROLES),
            function (SCollection $tags) { return $tags->where('user_id', Auth::id()); }
        )->pluck('name', 'id');
        
    }
    
}
