<?php
namespace App\Models;

use App\Facades\Datatable;
use App\Helpers\{ModelTrait, Snippet};
use Carbon\Carbon;
use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\{Builder,
    Collection,
    Model,
    Relations\BelongsTo,
    Relations\HasMany,
    Relations\HasManyThrough};
use Illuminate\Support\Facades\{Auth, DB, Session};
use Throwable;

/**
 * App\Models\Corp 企业
 *
 * @property int $id
 * @property string $name 企业名称
 * @property string $acronym 企业名称缩写（首字母缩略词）
 * @property string $corpid 企业号id
 * @property string $contact_sync_secret "通讯录同步"应用Secret
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $expire_at
 * @property int $enabled
 * @property int $menu_id 对应的菜单ID
 * @property int $company_id 所属运营者公司ID
 * @property int $department_id 对应的部门ID
 * @property string $encoding_aes_key 接收消息服务器配置项，用于加密消息体
 * @property string $token 接收消息服务器配置项，用于生成签名
 * @property string|null $access_token 通讯录同步应用access_token
 * @property int $departmentid 企业微信后台通讯录的根部门id
 * @property string|null $mchid 微信支付商户号
 * @property string|null $apikey 微信支付商户支付密钥
 * @property-read Company $company
 * @property-read Collection|Department[] $departments
 * @property-read Collection|Grade[] $grades
 * @property-read Collection|School[] $schools
 * @property-read Collection|Tag[] $tags
 * @property-read Department $department
 * @property-read Menu $menu
 * @property-read Collection|App[] $apps
 * @method static Builder|Corp whereCorpid($value)
 * @method static Builder|Corp whereContactSyncSecret($value)
 * @method static Builder|Corp whereCreatedAt($value)
 * @method static Builder|Corp whereEnabled($value)
 * @method static Builder|Corp whereId($value)
 * @method static Builder|Corp whereName($value)
 * @method static Builder|Corp whereAcronym($value)
 * @method static Builder|Corp whereUpdatedAt($value)
 * @method static Builder|Corp whereExpireAt($value)
 * @method static Builder|Corp whereEncodingAesKey($value)
 * @method static Builder|Corp whereToken($value)
 * @method static Builder|Corp whereCompanyId($value)
 * @method static Builder|Corp whereDepartmentId($value)
 * @method static Builder|Corp whereMenuId($value)
 * @method static Builder|Corp whereApikey($value)
 * @method static Builder|Corp whereMchid($value)
 * @method static Builder|Corp whereAccessToken($value)
 * @method static Builder|Corp newModelQuery()
 * @method static Builder|Corp newQuery()
 * @method static Builder|Corp query()
 * @mixin Eloquent
 */
class Corp extends Model {
    
    use ModelTrait;
    
    protected $fillable = [
        'name', 'acronym', 'company_id', 'expire_at',
        'corpid', 'contact_sync_secret', 'access_token',
        'encoding_aes_key', 'token', 'menu_id',
        'department_id', 'departmentid', 'mchid',
        'apikey', 'enabled',
    ];
    
    /**
     * 返回对应的部门对象
     *
     * @return BelongsTo
     */
    function department() { return $this->belongsTo('App\Models\Department'); }
    
    /**
     * 返回对应的菜单对象
     *
     * @return BelongsTo
     */
    function menu() { return $this->belongsTo('App\Models\Menu'); }
    
    /**
     * 获取所属运营者公司对象
     *
     * @return BelongsTo
     */
    function company() { return $this->belongsTo('App\Models\Company'); }
    
    /**
     * 获取指定企业包含的所有应用
     *
     * @return HasMany
     */
    function apps() { return $this->hasMany('App\Models\App'); }
    
    /**
     * 获取下属学校对象
     *
     * @return HasMany
     */
    function schools() { return $this->hasMany('App\Models\School'); }
    
    /**
     * 通过School中间对象获取所有年级对象
     *
     * @return HasManyThrough
     */
    function grades() {
        
        return $this->hasManyThrough('App\Models\Grade', 'App\Models\School');
        
    }
    
    /**
     * 通过School中间对象获取所有教职员工组对象
     *
     * @return HasManyThrough
     */
    function tags() {
        
        return $this->hasManyThrough('App\Models\Tag', 'App\Models\School');
        
    }
    
    /**
     * 企业列表
     *
     * @return mixed
     */
    function index() {
        
        $columns = [
            ['db' => 'Corp.id', 'dt' => 0],
            [
                'db'        => 'Corp.name', 'dt' => 1,
                'formatter' => function ($d) {
                    return Snippet::corp($d);
                },
            ],
            ['db' => 'Corp.acronym', 'dt' => 2],
            [
                'db'        => 'Company.name as companyname', 'dt' => 3,
                'formatter' => function ($d) {
                    return sprintf(Snippet::ICON, 'fa-building text-blue', '') .
                        '<span class="text-blue">' . $d . '</span>';
                },
            ],
            ['db' => 'Corp.corpid', 'dt' => 4],
            ['db' => 'Corp.contact_sync_secret', 'dt' => 5],
            ['db' => 'Corp.created_at', 'dt' => 6],
            ['db' => 'Corp.updated_at', 'dt' => 7],
            [
                'db'        => 'Corp.enabled', 'dt' => 8,
                'formatter' => function ($d, $row) {
                    return Datatable::status($d, $row, false);
                },
            ],
        ];
        $joins = [
            [
                'table'      => 'companies',
                'alias'      => 'Company',
                'type'       => 'INNER',
                'conditions' => [
                    'Company.id = Corp.company_id',
                ],
            ],
        ];
        
        return Datatable::simple(
            $this->getModel(), $columns, $joins
        );
        
    }
    
    /**
     * 保存企业
     *
     * @param array $data
     * @return mixed|bool|null
     * @throws Throwable
     */
    function store(array $data) {
        
        try {
            DB::transaction(function () use ($data) {
                # 创建企业微信、对应部门及菜单
                $corp = $this->create($data);
                $department = (new Department)->stow($corp, 'company');
                $menu = (new Menu)->stow($corp, 'company');
                # 更新“企业微信”的部门id和菜单id
                $corp->update([
                    'department_id' => $department->id,
                    'menu_id'       => $menu->id,
                ]);
            });
        } catch (Exception $e) {
            throw $e;
        };
        
        return true;
        
    }
    
    /**
     * 更新企业
     *
     * @param array $data
     * @param $id
     * @return mixed|bool|null
     * @throws Throwable
     */
    function modify(array $data, $id) {
        
        try {
            DB::transaction(function () use ($data, $id) {
                $corp = $this->find($id);
                $corp->update($data);
                (new Department())->alter($corp, 'company');
                (new Menu())->alter($corp, 'company');
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
    /**
     * 删除企业
     *
     * @param $id
     * @return bool
     * @throws Throwable
     */
    function remove($id = null) {
        
        return $this->del($this, $id);
        
    }
    
    /**
     * 删除指定企业的所有数据
     *
     * @param $id
     * @return bool
     * @throws Throwable
     */
    function purge($id) {
        
        try {
            DB::transaction(function () use ($id) {
                $corp = $this->find($id);
                $classes = ['App', 'School'];
                $keys = array_fill(0, sizeof($classes), 'corp_id');
                $values = array_fill(0, sizeof($classes), $id);
                array_map([$this, 'delRelated'], $keys, $classes, $values);
                (new Department)->remove($corp->department_id);
                (new Menu)->remove($corp->menu_id);
                $corp->delete();
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
    /**
     * 根据角色 & 菜单id获取corp_id
     *
     * @return int|mixed
     */
    function corpId() {
        
        if (!Session::exists('menuId')) return null;
        $user = Auth::user();
        switch ($user->role()) {
            case '运营':
            case '企业':
                $corpMenuId = (new Menu)->menuId(session('menuId'), '企业');
                $corpId = $corpMenuId ? $this->where('menu_id', $corpMenuId)->first()->id : null;
                break;
            case '学校':
                $schoolMenuId = (new Menu)->menuId(session('menuId'));
                $corpId = School::whereMenuId($schoolMenuId)->first()->corp_id;
                break;
            default:
                $corpId = School::find($user->educator->school_id)->corp_id;
                break;
        }
        
        return $corpId;
        
    }
    
}
