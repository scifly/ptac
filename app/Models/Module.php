<?php
namespace App\Models;

use App\Facades\Datatable;
use App\Helpers\Constant;
use App\Helpers\HttpStatusCode;
use App\Helpers\ModelTrait;
use App\Helpers\Snippet;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\View\View;

/**
 * App\Models\Module - 应用模块
 *
 * @property int $id
 * @property string $name
 * @property string $remark
 * @property int|null $tab_id
 * @property int $school_id 应用模块所属的学校id
 * @property int|null $order 模块位置
 * @property int $media_id 模块图标媒体id
 * @property string|null $uri
 * @property int $isfree 是否为免费模块
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $enabled
 * @property int|null $group_id 应用模块所属角色id
 * @property-read Media $media
 * @property-read School $school
 * @property-read Tab|null $tab
 * @property-read Group|null $group
 * @property-read Collection|Student[] $students
 * @method static Builder|Module whereCreatedAt($value)
 * @method static Builder|Module whereEnabled($value)
 * @method static Builder|Module whereId($value)
 * @method static Builder|Module whereIsfree($value)
 * @method static Builder|Module whereMediaId($value)
 * @method static Builder|Module whereName($value)
 * @method static Builder|Module whereOrder($value)
 * @method static Builder|Module whereRemark($value)
 * @method static Builder|Module whereSchoolId($value)
 * @method static Builder|Module whereTabId($value)
 * @method static Builder|Module whereUpdatedAt($value)
 * @method static Builder|Module whereUri($value)
 * @method static Builder|Module whereGroupId($value)
 * @mixin \Eloquent
 */
class Module extends Model {

    use ModelTrait;
    
    protected $fillable = [
        'name', 'remark', 'tab_id',
        'media_id', 'group_id', 'uri',
        'isfree', 'school_id', 'order',
        'enabled'
    ];
    
    /**
     * 返回模块所属的控制器对象
     *
     * @return BelongsTo
     */
    function tab() { return $this->belongsTo('App\Models\Tab'); }
    
    /**
     * 返回模块所属的媒体对象
     *
     * @return BelongsTo
     */
    function media() { return $this->belongsTo('App\Models\Media'); }
    
    /**
     * 返回指定模块所属的学校对象
     *
     * @return BelongsTo
     */
    function school() { return $this->belongsTo('App\Models\School'); }
    
    /**
     * 返回指定模块所属的角色对象
     *
     * @return BelongsTo
     */
    function group() { return $this->belongsTo('App\Models\Group'); }
    
    /**
     * 返回订阅了指定增值应用模块的所有学生对象
     *
     * @return BelongsToMany
     */
    function students() { return $this->belongsToMany('App\Models\Student', 'modules_students'); }
    
    /**
     * 应用模块列表
     *
     * @return array
     */
    function index() {
        
        $columns = [
            ['db' => 'Module.id', 'dt' => 0],
            ['db' => 'Module.name', 'dt' => 1],
            [
                'db' => 'Module.school_id', 'dt' => 2,
                'formatter' => function ($d) {
                    return Snippet::school(School::find($d)->name);
                }
            ],
            [
                'db' => 'Module.tab_id', 'dt' => 3,
                'formatter' => function ($d) {
                    return $d ? Tab::find($d)->comment : '-';
                }
            ],
            [
                'db' => 'Module.uri', 'dt' => 4,
                'formatter' => function ($d) {
                    return $d ?? '-';
                }
            ],
            [
                'db' => 'Module.group_id', 'dt' => 5,
                'formatter' => function ($d) {
                    return !empty($d) ? Group::find($d)->name : '公用';
                }
            ],
            [
                'db' => 'Module.isfree', 'dt' => 6,
                'formatter' => function ($d) {
                    return $d ? '基本' : '增值';
                }
            ],
            ['db' => 'Module.created_at', 'dt' => 7, 'dr' => true],
            ['db' => 'Module.updated_at', 'dt' => 8, 'dr' => true],
            [
                'db' => 'Module.enabled', 'dt' => 9,
                'formatter' => function ($d, $row) {
                    return Datatable::status($d, $row, false);
                }
            ]
        ];
        $joins = [
            [
                'table' => 'medias',
                'alias' => 'Media',
                'type' => 'INNER',
                'conditions' => [
                    'Media.id = Module.media_id'
                ]
            ]
        ];
        $condition = 'Module.school_id IN (' . implode(',', $this->schoolIds()) . ')';
        
        return Datatable::simple(
            $this->getModel(), $columns, $joins, $condition
        );
        
    }
    
    /**
     * 保存应用模块
     *
     * @param array $data
     * @return bool
     */
    function store(array $data) {
        
        return $this->create($data) ? true : false;
        
    }
    
    /**
     * 更新应用模块
     *
     * @param array $data
     * @param $id
     * @return bool
     */
    function modify(array $data, $id) {
        
        return $this->find($id)->update($data);
        
    }
    
    /**
     * @param $id
     * @return bool|null
     * @throws Exception
     */
    function remove($id) {
        
        return $this->find($id)->delete();
        
    }
    
    /**
     * 上传应用模块图标
     *
     * @return JsonResponse
     */
    function upload() {
        
        $file = Request::file('file');
        abort_if(
            empty($file),
            HttpStatusCode::NOT_ACCEPTABLE,
            __('messages.empty_file')
        );
        $uploadedFile = (new Media())->upload(
            $file, __('messages.wap_site_module.title')
        );
        abort_if(
            !$uploadedFile,
            HttpStatusCode::INTERNAL_SERVER_ERROR,
            __('messages.file_upload_failed')
        );
        
        return response()->json($uploadedFile);
        
    }
    
    /**
     * 返回应用首页
     *
     * @return Factory|View
     */
    function wIndex() {
    
        $role = Auth::user()->role();
        $custodianGroupId = Group::whereName('监护人')->first()->id;
        $schoolId = session('schoolId');
        $modules = $this->orderBy('order')->where([
            'school_id' => $schoolId, 'enabled' => 1,
        ])->get()->filter(
            function (Module $module) use ($role, $schoolId, $custodianGroupId) {
                $moduleGroupId = $module->group_id;
                if (in_array($role, Constant::SUPER_ROLES)) {
                    return $moduleGroupId != $custodianGroupId;
                } elseif ($role == '监护人') {
                    return in_array($moduleGroupId, [0, $custodianGroupId]);
                } else {
                    $userGroupId = Group::where([
                        'enabled' => 1, 'school_id' => $schoolId, 'name' => $role,
                    ])->first()->id;
                    return in_array($moduleGroupId, [0, $userGroupId]);
                }
            }
        );
        foreach ($modules as &$module) {
            if ($module->tab_id) {
                $tab = Tab::find($module->tab_id);
                if ($tab->action_id) {
                    $module->uri = str_replace(
                        '{acronym}', session('acronym'),
                        Action::find($tab->action_id)->route
                    );
                }
            }
        }
    
        return view('wechat.wechat.index', [
            'modules' => $modules
        ]);
        
    }
    
    /**
     * 返回对当前用户可见的学校列表
     *
     * @return Factory|View
     */
    function schools() {
    
        $user = Auth::user();
        $schoolIds = $user->schoolIds($user->id, session('corpId'));
    
        return view('wechat.schools', [
            'schools' => School::whereIn('id', $schoolIds)->pluck('name', 'id')
        ]);
        
    }
    
}
