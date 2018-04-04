<?php

namespace App\Models;

use App\Events\CorpCreated;
use App\Events\CorpDeleted;
use App\Events\CorpUpdated;
use App\Facades\DatatableFacade as Datatable;
use App\Helpers\ModelTrait;
use App\Helpers\Snippet;
use Carbon\Carbon;
use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

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
 * @property int $enabled
 * @property int $menu_id 对应的菜单ID
 * @property int $company_id 所属运营者公司ID
 * @property int $department_id 对应的部门ID
 * @method static Builder|Corp whereCorpid($value)
 * @method static Builder|Corp whereContactSyncSecret($value)
 * @method static Builder|Corp whereCreatedAt($value)
 * @method static Builder|Corp whereEnabled($value)
 * @method static Builder|Corp whereId($value)
 * @method static Builder|Corp whereName($value)
 * @method static Builder|Corp whereAcronym($value)
 * @method static Builder|Corp whereUpdatedAt($value)
 * @method static Builder|Corp whereCompanyId($value)
 * @method static Builder|Corp whereDepartmentId($value)
 * @method static Builder|Corp whereMenuId($value)
 * @mixin Eloquent
 * @property-read Company $company
 * @property-read Collection|Department[] $departments
 * @property-read Collection|Grade[] $grades
 * @property-read Collection|School[] $schools
 * @property-read Collection|Team[] $teams
 * @property-read Department $department
 * @property-read Menu $menu
 */
class Corp extends Model {

    use ModelTrait;

    protected $fillable = [
        'name', 'acronym', 'company_id',
        'corpid', 'contact_sync_secret',
        'menu_id', 'department_id', 'enabled',
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
    function teams() {

        return $this->hasManyThrough('App\Models\Team', 'App\Models\School');

    }
    
    /**
     * 保存企业
     *
     * @param array $data
     * @param bool $fireEvent
     * @return bool
     */
    function store(array $data, $fireEvent = false) {

        $corp = $this->create($data);
        if ($corp && $fireEvent) {
            event(new CorpCreated($corp));
            return true;
        }

        return $corp ? true : false;

    }

    /**
     * 更新企业
     *
     * @param array $data
     * @param $id
     * @param bool $fireEvent
     * @return bool
     */
    function modify(array $data, $id, $fireEvent = false) {

        $corp = $this->find($id);
        $updated = $corp->update($data);
        if ($updated && $fireEvent) {
            event(new CorpUpdated($corp));
            return true;
        }

        return $updated ? true : false;

    }
    
    /**
     * 删除企业
     *
     * @param $id
     * @param bool $fireEvent
     * @return bool
     * @throws Exception
     */
    function remove($id, $fireEvent = false) {

        $corp = $this->find($id);
        if (!$corp) { return false; }
        $removed = $this->removable($corp) ? $corp->delete() : false;
        if ($removed && $fireEvent) {
            event(new CorpDeleted($corp));
            return true;
        }

        return $removed ? true : false;

    }

    /**
     * 根据角色 & 菜单id获取corp_id
     *
     * @return int|mixed
     */
    function corpId() {

        $user = Auth::user();
        if (!Session::exists('menuId')) { return null; }
        switch ($user->group->name) {
            case '运营':
            case '企业':
                $menu = new Menu();
                $corpMenuId = $menu->menuId(session('menuId'), '企业');
                unset($menu);
                return $corpMenuId ? $this->whereMenuId($corpMenuId)->first()->id : null;
            case '学校':
                $departmentId = $user->topDeptId();
                return School::whereDepartmentId($departmentId)->first()->corp_id;
            default:
                return School::find($user->educator->school_id)->corp_id;
        }

    }

    /**
     * 企业列表
     *
     * @return mixed
     */
    function datatable() {

        $columns = [
            ['db' => 'Corp.id', 'dt' => 0],
            [
                'db' => 'Corp.name', 'dt' => 1,
                'formatter' => function ($d) {
                    return sprintf(Snippet::ICON, 'fa-weixin') . $d;
                }
            ],
            ['db' => 'Corp.acronym', 'dt' => 2],
            [
                'db' => 'Company.name as companyname', 'dt' => 3,
                'formatter' => function ($d) {
                    return sprintf(Snippet::ICON, 'fa-building') . $d;
                }
            ],
            ['db' => 'Corp.corpid', 'dt' => 4],
            ['db' => 'Corp.contact_sync_secret', 'dt' => 5],
            ['db' => 'Corp.created_at', 'dt' => 6],
            ['db' => 'Corp.updated_at', 'dt' => 7],
            [
                'db' => 'Corp.enabled', 'dt' => 8,
                'formatter' => function ($d, $row) {
                    return Datatable::dtOps($d, $row, false);
                },
            ],
        ];
        $joins = [
            [
                'table' => 'companies',
                'alias' => 'Company',
                'type' => 'INNER',
                'conditions' => [
                    'Company.id = Corp.company_id',
                ],
            ],
        ];

        return Datatable::simple(
            $this->getModel(), $columns, $joins
        );

    }

}
