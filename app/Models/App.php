<?php
namespace App\Models;

use App\Facades\Datatable;
use App\Helpers\{ModelTrait};
use App\Jobs\SyncApp;
use Eloquent;
use Illuminate\Database\Eloquent\{Builder, Collection, Model, Relations\BelongsTo, Relations\HasMany};
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\{Auth, Request};
use Throwable;

/**
 * Class App - 应用
 *
 * @property int $id
 * @property int $corp_id 所属企业id
 * @property int $category 应用类型：1 - 企业应用；2 - 公众号；3 - （企业）通讯录同步
 * @property string $name 企业应用 / 公众号名称
 * @property string|null $appid 企业应用agentid / 公众号appid；null - 通讯录同步(企业微信)
 * @property string $appsecret 企业应用secret / 公众号appsecret
 * @property mixed|null $menu 企业应用 / 公众号菜单
 * @property mixed $properties 企业应用 / 公众号参数(token, encoding_aes_key ...)
 * @property string|null $description 企业应用 / 公众号描述
 * @property string|null $access_token
 * @property string|null $expire_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $enabled
 * @property-read Corp $corp
 * @property-read Collection|Message[] $messages
 * @property-read int|null $messages_count
 * @property-read Collection|Openid[] $openids
 * @property-read int|null $openids_count
 * @property-read Collection|School[] $schools
 * @property-read int|null $schools_count
 * @property-read Collection|Template[] $templates
 * @property-read int|null $templates_count
 * @method static Builder|App newModelQuery()
 * @method static Builder|App newQuery()
 * @method static Builder|App query()
 * @method static Builder|App whereAccessToken($value)
 * @method static Builder|App whereAppid($value)
 * @method static Builder|App whereAppsecret($value)
 * @method static Builder|App whereCategory($value)
 * @method static Builder|App whereCorpId($value)
 * @method static Builder|App whereCreatedAt($value)
 * @method static Builder|App whereDescription($value)
 * @method static Builder|App whereEnabled($value)
 * @method static Builder|App whereExpireAt($value)
 * @method static Builder|App whereId($value)
 * @method static Builder|App whereMenu($value)
 * @method static Builder|App whereName($value)
 * @method static Builder|App whereProperties($value)
 * @method static Builder|App whereUpdatedAt($value)
 * @mixin Eloquent
 */
class App extends Model {
    
    use ModelTrait;
    
    protected $fillable = [
        'corp_id', 'category', 'name', 'appid',
        'appsecret', 'menu', 'properties',
        'description', 'access_token',
        'expire_at', 'enabled',
        
        'properties->url',
        'properties->token',
        'properties->encoding_aes_key',
        'properties->primary_industry',
        'properties->secondary_industry',
        'properties->redirect_domain',
        'properties->report_location_flag',
        'properties->isreportenter',
        'properties->home_url',
        'properties->type'      # 公众号类型：0 - 订阅号，1 - 服务号
    ];
    
    const CATEGORY = [
        1 => '企业应用',
        2 => '公众号',
        3 => '管理工具',
    ];
    /** Properties -------------------------------------------------------------------------------------------------- */
    /** @return BelongsTo */
    function corp() { return $this->belongsTo('App\Models\Corp'); }
    
    /** @return HasMany */
    function messages() { return $this->hasMany('App\Models\Message'); }
    
    /** @return HasMany */
    function schools() { return $this->hasMany('App\Models\School'); }
    
    /** @return HasMany */
    function openids() { return $this->hasMany('App\Models\Openid'); }
    
    /** @return HasMany */
    function templates() { return $this->hasMany('App\Models\Template'); }
    
    /** crud -------------------------------------------------------------------------------------------------------- */
    /**
     * 应用列表
     *
     * @return array|JsonResponse
     */
    function index() {
        
        $columns = [
            ['db' => 'App.id', 'dt' => 0],
            ['db' => 'App.name', 'dt' => 1],
            [
                'db'        => 'App.category', 'dt' => 2,
                'formatter' => function ($d) {
                    return self::CATEGORY[$d];
                },
            ],
            [
                'db'        => 'Corp.name as corpname', 'dt' => 3,
                'formatter' => function ($d) {
                    return $this->iconHtml($d,'corp');
                },
            ],
            ['db' => 'App.description', 'dt' => 4],
            ['db' => 'App.created_at', 'dt' => 5],
            ['db' => 'App.updated_at', 'dt' => 6],
            [
                'db'        => 'App.enabled', 'dt' => 7,
                'formatter' => function ($d, $row) {
                    return Datatable::status($d, $row, false);
                },
            ],
        ];
        $joins = [
            [
                'table'      => 'corps',
                'alias'      => 'Corp',
                'type'       => 'INNER',
                'conditions' => [
                    'Corp.id = App.corp_id',
                ],
            ],
        ];
        
        return Datatable::simple(
            $this, $columns, $joins,
            'App.corp_id = ' . $this->corpId()
        );
        
    }
    
    /**
     * 保存新创建的app
     *
     * @param array $data
     * @return bool
     */
    function store(array $data) {
        
        SyncApp::dispatch($data, Auth::id());
        
        return true;
        
    }
    
    /**
     * 更新App
     *
     * @param array $data
     * @param $id
     * @return bool
     */
    function modify(array $data, $id) {
        
        SyncApp::dispatch($data, Auth::id(), $id);
        
        return true;
        
    }
    
    /**
     * 移除应用
     *
     * @param $id
     * @return bool|null
     * @throws Throwable
     */
    function remove($id = null) {
    
        return $this->purge($id, [
            'reset.app_id' => ['Message', 'School', 'Openid', 'Template']
        ]);
    
    }
    
    /** Helper functions -------------------------------------------------------------------------------------------- */
    /** @return array */
    function compose() {
        
        $corpId = $this->corpId();
        switch (Request::route()->uri) {
            case 'apps/index':
                return [
                    'titles' => [
                        '#', '名称', '类型', '所属企业', '描述',
                        '创建于', '更新于', '状态 . 操作',
                    ],
                ];
            case 'apps/create':
                return [
                    'corpId'     => $corpId,
                    'categories' => self::CATEGORY,
                ];
            default:    # 编辑应用
                return array_merge(
                    ['corpId' => $corpId],
                    json_decode(
                        $this->find(Request::route('id'))->properties, true
                    ) ?? []
                );
        }
        
    }
    
}