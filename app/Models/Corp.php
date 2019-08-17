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
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\{Auth, DB, Request, Session};
use Throwable;

/**
 * Class Corp
 * @package App\Models
 */
class Corp extends Model {
    
    use ModelTrait;
    
    protected $fillable = [
        'name', 'acronym', 'company_id', 'corpid',
        'menu_id', 'department_id', 'departmentid', 'mchid',
        'apikey', 'sms_balance', 'sms_used', 'enabled',
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
     * 通过School中间对象获取所有教职员工对象
     *
     * @return HasManyThrough
     */
    function educators() {
        
        return $this->hasManyThrough('App\Models\Educator', 'App\Models\School');
        
    }
    
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
                    return Snippet::icon($d, 'corp');
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
            ['db' => 'Corp.created_at', 'dt' => 5],
            ['db' => 'Corp.updated_at', 'dt' => 6],
            [
                'db'        => 'Corp.enabled', 'dt' => 7,
                'formatter' => function ($d, $row) {
                    $rechargeLink = sprintf(
                        Snippet::DT_ANCHOR,
                        'recharge_' . $row['id'],
                        '短信充值 & 查询', 'fa-money'
                    );
                    
                    return Datatable::status($d, $row, false) .
                        (Auth::user()->can('act', self::uris()['recharge']) ? $rechargeLink : '');
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
     * 短信充值
     *
     * @param $id
     * @param array $data
     * @return JsonResponse
     * @throws Throwable
     */
    function recharge($id, array $data) {
        
        return (new SmsCharge)->recharge($this, $id, $data);
        
    }
    
    /**
     * 删除企业
     *
     * @param $id
     * @return bool
     * @throws Throwable
     */
    function remove($id = null) {
        
        try {
            DB::transaction(function () use ($id) {
                $ids = $id ? [$id] : array_values(Request::input('ids'));
                $corps = $this->whereIn('id', $ids)->get();
                list($appIds, $schoolIds) = array_map(
                    function ($class) use ($ids) {
                        return $this->model($class)->whereIn('corp_id', $ids)
                            ->pluck('id')->toArray();
                    }, ['App', 'School']
                );
                list($departmentIds, $menuIds) = array_map(
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
    
    /**
     * 返回composer所需view数据
     *
     * @return array
     */
    function compose() {
        
        switch (explode('/', Request::path())[1]) {
            case 'index':
                return [
                    'titles' => [
                        '#', '名称', '缩写', '所属运营', '企业号ID', '通讯录同步Secret',
                        '创建于', '更新于', '状态 . 操作',
                    ],
                ];
            case 'create':
            case 'edit':
                $companies = Company::pluck('name', 'id');
                if ((new Menu)->menuId(session('menuId'), '企业')) {
                    # disabled - 是否显示'返回列表'和'取消'按钮
                    if (Request::route('id')) {
                        $corp = Corp::find(Request::route('id'));
                        $companies = [$corp->company_id => $corp->company->name];
                        $disabled = true;
                    }
                    
                    return [
                        'companies' => $companies,
                        'disabled'  => $disabled ?? null,
                    ];
                } else {
                    return [
                        'companies' => $companies,
                    ];
                }
            default:
                return (new Message)->compose('recharge');
        }
        
    }
    
}
