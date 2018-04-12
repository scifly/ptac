<?php

namespace App\Models;

use App\Facades\DatatableFacade as Datatable;
use App\Helpers\HttpStatusCode;
use App\Helpers\ModelTrait;
use App\Http\Requests\WapSiteRequest;
use Carbon\Carbon;
use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Throwable;

/**
 * App\Models\WapSite 微网站
 *
 * @property int $id
 * @property int $school_id 所属学校ID
 * @property string $site_title 首页抬头
 * @property string $media_ids 首页幻灯片图片多媒体ID
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $enabled
 * @property-read School $school
 * @property-read Collection|\App\Models\WapSiteModule[] $wapSiteModules
 * @method static Builder|WapSite whereCreatedAt($value)
 * @method static Builder|WapSite whereEnabled($value)
 * @method static Builder|WapSite whereId($value)
 * @method static Builder|WapSite whereMediaIds($value)
 * @method static Builder|WapSite whereSchoolId($value)
 * @method static Builder|WapSite whereSiteTitle($value)
 * @method static Builder|WapSite whereUpdatedAt($value)
 * @mixin Eloquent
 */
class WapSite extends Model {
    
    use ModelTrait;
    
    protected $department, $media;

    protected $fillable = [
        'id', 'school_id', 'site_title',
        'media_ids', 'created_at', 'updated_at',
        'enabled',
    ];
    
    function __construct(array $attributes = []) {
        
        parent::__construct($attributes);
        $this->department = app()->make('App\Models\Department');
        $this->media = app()->make('App\Models\Media');
        
    }
    
    /**
     * 获取微网站包含的所有网站模块对象
     *
     * @return HasMany
     */
    function wapSiteModules() { return $this->hasMany('App\Models\WapSiteModule'); }
    
    /**
     * 返回微网站所属的学校对象
     *
     * @return BelongsTo
     */
    function school() { return $this->belongsTo('App\Models\School'); }

    /**
     * 微网站列表
     *
     * @return array
     */
    function datatable() {

        $columns = [
            ['db' => 'WapSite.id', 'dt' => 0],
            ['db' => 'School.name', 'dt' => 1],
            ['db' => 'WapSite.site_title', 'dt' => 2],
            ['db' => 'WapSite.created_at', 'dt' => 3],
            ['db' => 'WapSite.updated_at', 'dt' => 4],
            [
                'db' => 'WapSite.enabled', 'dt' => 5,
                'formatter' => function ($d, $row) {
                    return Datatable::dtOps($d, $row);
                },
            ],
        ];
        $joins = [
            [
                'table' => 'schools',
                'alias' => 'School',
                'type' => 'INNER',
                'conditions' => [
                    'School.id = WapSite.school_id',
                ],
            ],
        ];
        $condition = 'WapSite.school_id = ' . $this->schoolId();
    
        return Datatable::simple(
            $this->getModel(), $columns, $joins, $condition
        );
        
    }
    
    /**
     * @param WapSiteRequest $request
     * @return bool
     * @throws Exception
     * @throws Throwable
     */
    function store(WapSiteRequest $request) {
        
        try {
            DB::transaction(function () use ($request) {
                //删除原有的图片
                self::removeMedias($request);
                self::create($request->all());
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
    /**
     * 更新微网站
     *
     * @param WapSiteRequest $request
     * @param $id
     * @return bool|mixed
     * @throws Exception
     * @throws Throwable
     */
    function modify(WapSiteRequest $request, $id) {
        
        $wapSite = self::find($id);
        if (!$wapSite) { return false; }
        try {
            DB::transaction(function () use ($request, $id) {
                self::removeMedias($request);
                return self::find($id)->update(
                    $request->except('_method', '_token', 'del_ids')
                );
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
    /**
     * 返回微官网首页
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    function wIndex() {
    
        $user = Auth::user();
        abort_if(
            !$user || in_array($user->group->name, ['运营', '企业']),
            HttpStatusCode::UNAUTHORIZED,
            __('messages.unauthorized')
        );
        $schoolId = Group::find($user->group_id)->school_id;
        if (!$schoolId) {
            $departmentId = DepartmentUser::whereUserId($user->id)->first()->department_id;
            $department = $this->department->schoolDeptId($departmentId);
            $schoolId = School::whereDepartmentId($department)->first()->id;
        }
        $wapSite = WapSite::whereSchoolId($schoolId)->first();
        abort_if(
            !$wapSite,
            HttpStatusCode::NOT_FOUND,
            __('messages.not_found')
        );
        
        return view('wechat.wapsite.home', [
            'wapsite' => $wapSite,
            'medias'  => $this->media->medias(
                explode(',', $wapSite->media_ids)
            ),
        ]);
        
    }
    
    /**
     * @param $request
     * @throws Exception
     */
    private function removeMedias(WapSiteRequest $request) {
        
        //删除原有的图片
        $mediaIds = $request->input('del_ids');
        if ($mediaIds) {
            $medias = Media::whereIn('id', $mediaIds)->get(['id', 'path']);
            foreach ($medias as $media) {
                Storage::disk('uploads')->delete($media->path);
            }
            try {
                Media::whereIn('id', $mediaIds)->delete();
            } catch (Exception $e) {
                throw $e;
            }
        }
        
    }
    
}
