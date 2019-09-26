<?php
namespace App\Models;

use App\Facades\Datatable;
use App\Helpers\ModelTrait;
use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\{Builder, Model, Relations\BelongsTo, Relations\HasMany, Relations\HasManyThrough};
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\{Auth, DB, Request};
use Throwable;

/**
 * Class Corp
 *
 * @property int $id
 * @property string $name 企业名称
 * @property int $company_id 所属运营者公司ID
 * @property string $acronym 企业名称缩写（首字母缩略词）
 * @property string $corpid 企业id
 * @property int $department_id 对应的部门ID
 * @property int $menu_id 对应的菜单id
 * @property int $departmentid 企业微信后台通讯录的根部门id
 * @property string|null $mchid 微信支付商户号
 * @property string|null $apikey 微信支付商户支付密钥
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $sms_balance 短信余额
 * @property int $sms_used 已使用的短信数量
 * @property int $enabled
 * @property-read Collection|App[] $apps
 * @property-read int|null $apps_count
 * @property-read Company $company
 * @property-read Department $department
 * @property-read Collection|Educator[] $educators
 * @property-read int|null $educators_count
 * @property-read Collection|Grade[] $grades
 * @property-read int|null $grades_count
 * @property-read Menu $menu
 * @property-read Collection|School[] $schools
 * @property-read int|null $schools_count
 * @property-read Collection|Tag[] $tags
 * @property-read int|null $tags_count
 * @method static Builder|Corp newModelQuery()
 * @method static Builder|Corp newQuery()
 * @method static Builder|Corp query()
 * @method static Builder|Corp whereAcronym($value)
 * @method static Builder|Corp whereApikey($value)
 * @method static Builder|Corp whereCompanyId($value)
 * @method static Builder|Corp whereCorpid($value)
 * @method static Builder|Corp whereCreatedAt($value)
 * @method static Builder|Corp whereDepartmentId($value)
 * @method static Builder|Corp whereEnabled($value)
 * @method static Builder|Corp whereId($value)
 * @method static Builder|Corp whereMchid($value)
 * @method static Builder|Corp whereMenuId($value)
 * @method static Builder|Corp whereName($value)
 * @method static Builder|Corp whereSmsBalance($value)
 * @method static Builder|Corp whereSmsUsed($value)
 * @method static Builder|Corp whereUpdatedAt($value)
 * @mixin Eloquent
 * @property-read Department $dept
 */
class Corp extends Model {
    
    use ModelTrait;
    
    protected $fillable = [
        'name', 'company_id', 'acronym', 'corpid',
        'department_id',  'menu_id', 'departmentid', 'mchid',
        'apikey', 'sms_balance', 'sms_used', 'enabled',
    ];
    
    /** @return BelongsTo */
    function dept() { return $this->belongsTo('App\Models\Department', 'department_id'); }
    
    /** @return BelongsTo */
    function menu() { return $this->belongsTo('App\Models\Menu'); }
    
    /** @return BelongsTo */
    function company() { return $this->belongsTo('App\Models\Company'); }
    
    /** @return HasMany */
    function apps() { return $this->hasMany('App\Models\App'); }
    
    /** @return HasMany */
    function schools() { return $this->hasMany('App\Models\School'); }
    
    /** @return HasManyThrough */
    function educators() { return $this->hasManyThrough('App\Models\Educator', 'App\Models\School'); }
    
    /** @return HasManyThrough */
    function grades() { return $this->hasManyThrough('App\Models\Grade', 'App\Models\School'); }
    
    /** @return HasManyThrough */
    function tags() { return $this->hasManyThrough('App\Models\Tag', 'App\Models\School'); }
    
    /** @return mixed */
    function index() {
        
        $columns = [
            ['db' => 'Corp.id', 'dt' => 0],
            [
                'db'        => 'Corp.name', 'dt' => 1,
                'formatter' => function ($d) {
                    return $this->iconHtml($d, 'corp');
                },
            ],
            ['db' => 'Corp.acronym', 'dt' => 2],
            [
                'db'        => 'Company.name as companyname', 'dt' => 3,
                'formatter' => function ($d) {
                    return $this->iconHtml($d, 'company');
                },
            ],
            ['db' => 'Corp.corpid', 'dt' => 4],
            ['db' => 'Corp.created_at', 'dt' => 5],
            ['db' => 'Corp.updated_at', 'dt' => 6],
            [
                'db'        => 'Corp.enabled', 'dt' => 7,
                'formatter' => function ($d, $row) {
                    $rechargeLink = $this->anchor(
                        'recharge_' . $row['id'],
                        '短信充值 & 查询',
                        'fa-money'
                    );

                    return Datatable::status($d, $row, false) .
                        (Auth::user()->can('act', (new Action)->uris()['recharge']) ? $rechargeLink : '');
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
            $this, $columns, $joins
        );
        
    }
    
    /**
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
                (new Department)->alter($corp, 'company');
                (new Menu)->alter($corp, 'company');
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
    /**
     * @param $id
     * @param array $data
     * @return JsonResponse
     * @throws Throwable
     */
    function recharge($id, array $data) {
        
        return (new SmsCharge)->recharge($this, $id, $data);
        
    }
    
    /**
     * @param $id
     * @return bool
     * @throws Throwable
     */
    function remove($id = null) {
        
        try {
            DB::transaction(function () use ($id) {
                $ids = $id ? [$id] : array_values(Request::input('ids'));
                $corps = $this->whereIn('id', $ids)->get();
                [$appIds, $schoolIds] = array_map(
                    function ($class) use ($ids) {
                        return $this->model($class)->whereIn('corp_id', $ids)
                            ->pluck('id')->toArray();
                    }, ['App', 'School']
                );
                [$departmentIds, $menuIds] = array_map(
                    function ($field) use ($corps) {
                        return $corps->pluck($field)->toArray();
                    }, ['department_id', 'menu_id']
                );
                array_map(
                    function ($class, $ids) {
                        Request::replace(['ids' => $ids]);
                        $this->model($class)->remove();
                    }, ['Department', 'Menu', 'App', 'School'],
                    [$departmentIds, $menuIds, $appIds, $schoolIds]
                );
                Request::replace(['ids' => $ids]);
                $this->purge([class_basename($this)], 'id');
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
    /**
     * @return array
     * @throws Exception
     */
    function compose() {
        
        switch (explode('/', Request::path())[1]) {
            case 'index':
                return [
                    'titles' => [
                        '#', '名称', '缩写', '所属运营', '企业号ID',
                        '创建于', '更新于', '状态 . 操作',
                    ],
                ];
            case 'create':
            case 'edit':
                return ['companies' => Company::pluck('name', 'id')];
            default:
                return (new Message)->compose('recharge');
        }
        
    }
    
}
