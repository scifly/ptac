<?php
namespace App\Models;

use App\Facades\Datatable;
use App\Helpers\{Constant, ModelTrait};
use App\Jobs\SyncTag;
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
 * @property int $tagid 公众号标签id
 * @property string|null $remark 备注
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $enabled
 * @property int $synced 同步状态
 * @property-read Collection|User[] $users
 * @property-read School $school
 * @property-read User $creator
 * @property-read Collection|Department[] $departments
 * @property-read int|null $departments_count
 * @property-read int|null $users_count
 * @method static Builder|Tag whereCreatedAt($value)
 * @method static Builder|Tag whereEnabled($value)
 * @method static Builder|Tag whereId($value)
 * @method static Builder|Tag whereName($value)
 * @method static Builder|Tag whereRemark($value)
 * @method static Builder|Tag whereTagid($value)
 * @method static Builder|Tag whereSchoolId($value)
 * @method static Builder|Tag whereUserId($value)
 * @method static Builder|Tag whereUpdatedAt($value)
 * @method static Builder|Tag whereSynced($value)
 * @method static Builder|Tag newModelQuery()
 * @method static Builder|Tag newQuery()
 * @method static Builder|Tag query()
 * @mixin Eloquent
 * @property-read Collection|Department[] $depts
 * @property-read int|null $depts_count
 */
class Tag extends Model {
    
    use ModelTrait;
    
    protected $fillable = [
        'name', 'school_id', 'user_id', 'tagid',
        'remark', 'enabled', 'synced',
    ];
    
    /** @return BelongsTo */
    function school() { return $this->belongsTo('App\Models\School'); }
    
    /** @return BelongsTo */
    function creator() { return $this->belongsTo('App\Models\User'); }
    
    /** @return BelongsToMany */
    function users() { return $this->belongsToMany('App\Models\User', 'tags_users'); }
    
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
            [
                'db'        => 'Tag.name', 'dt' => 1,
                'formatter' => function ($d) {
                    return explode('.', $d)[0];
                },
            ],
            ['db' => 'Tag.remark', 'dt' => 2],
            ['db' => 'Tag.created_at', 'dt' => 3],
            ['db' => 'Tag.updated_at', 'dt' => 4],
            [
                'db'        => 'Tag.synced', 'dt' => 5,
                'formatter' => function ($d) {
                    return $d ? '是' : '否';
                },
            ],
            [
                'db'        => 'Tag.enabled', 'dt' => 6,
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
                !$tag ?: $this->sync([$tag->id], 'create');
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
        
        try {
            DB::transaction(function () use ($data, $id) {
                if ($id) {
                    $tag = $this->find($id);
                    TagUser::whereTagId($tag->id)->delete();
                    DepartmentTag::whereTagId($tag->id)->delete();
                    !isset($data['user_ids']) ?: $this->retain(
                        'TagUser', $id, $data['user_ids']
                    );
                    !isset($data['dept_ids']) ?: $this->retain(
                        'DepartmentTag', $tag->id, $data['dept_ids'], false
                    );
                    $tag->update($data);
                }
                $this->sync($id ? [$id] : Request::input('ids'), 'update');
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
    /**
     * 删除标签
     *
     * @param null $id
     * @return bool|null
     * @throws Throwable
     */
    function remove($id = null) {
        
        try {
            DB::transaction(function () use ($id) {
                $ids = $id ? [$id] : array_values(Request::input('ids'));
                $this->sync($ids, 'delete');
                $this->purge(
                    [class_basename($this), 'TagUser', 'DepartmentTag'],
                    'tag_id', 'purge', $ids
                );
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
    /**
     * 返回view所需数据
     *
     * @param null $action
     * @param Model|null $model
     * @return array
     * @throws Exception
     */
    function compose($action = null, Model $model = null) {
        
        $action = $action ?? explode('/', Request::path())[1];
        switch ($action) {
            case 'index':
                return [
                    'titles' => ['#', '名称', '备注', '创建于', '更新于', '同步', '状态 . 操作'],
                ];
            case 'user':
            case 'department':
            case 'message':
                if ($model) {
                    if ($action != 'message') {
                        $builder = $model->{'tags'};
                    } else {
                        $totag = explode(',', json_decode($model->{'content'}, true)['totag']);
                        $builder = $this->whereIn('tagid', $totag);
                    }
                    $selectedTags = $builder->pluck('name', 'id')->toArray();
                }
                
                return [
                    'tags'         => $this->list(),
                    'selectedTags' => $selectedTags ?? null,
                ];
            default:
                $tag = Tag::find(Request::route('id'));
                $departments = $tag ? $tag->depts : null;
                $users = $tag ? $tag->users : null;
                $targetIds = $departments ? $departments->pluck('id') : null;
                $targetsHtml = $users ? (new Message)->targetsHtml($tag->users, $targetIds) : null;
                
                return [
                    'targets'   => $targetsHtml ?? null,
                    'targetIds' => isset($targetIds) ? $targetIds->join(',') : '',
                ];
        }
        
    }
    
    /**
     * 返回对当前登录用户可见的标签列表
     *
     * @param null $schoolId
     * @return SCollection
     * @throws Exception
     */
    function list($schoolId = null) {
        
        $tags = $this->whereSchoolId($schoolId ?? $this->schoolId())->get();
        if (!in_array(Auth::user()->role(), Constant::SUPER_ROLES)) {
            $userIds = collect(explode(',', $this->visibleUserIds()));
            $deptIds = collect($this->departmentIds());
            $tags->filter(function (Tag $tag) use ($userIds, $deptIds) {
                return $userIds->has($tag->users->pluck('id')) &&
                    $deptIds->has($tag->depts->pluck('id'));
            });
        }
        $list = collect([]);
        foreach ($tags->pluck('name', 'id') as $id => $name) {
            $list[$id] = explode('.', $name)[0];
        }
        
        return collect([''])->union($list);
        
    }
    
    /**
     * 同步标签
     *
     * @param array $ids
     * @param $action
     */
    private function sync(array $ids, $action) {
        
        empty($ids) ?: SyncTag::dispatch($ids, Auth::id(), $action);
        
    }
    
}
