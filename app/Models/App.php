<?php
namespace App\Models;

use App\Facades\{Datatable, Wechat};
use App\Helpers\{Constant, HttpStatusCode, ModelTrait, Snippet};
use App\Http\Requests\AppRequest;
use App\Jobs\SyncApp;
use Carbon\Carbon;
use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\{Builder, Collection, Model, Relations\BelongsTo, Relations\HasMany};
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\{Auth, DB};
use Throwable;

/**
 * App\Models\App
 *
 * @property int $id
 * @property int $corp_id 所属企业id
 * @property string $name 应用名称
 * @property string $secret 应用Secret
 * @property string $description 应用备注
 * @property string $agentid 应用id
 * @property int $report_location_flag 企业应用是否打开地理位置上报 0：不上报；1：进入会话上报；2：持续上报
 * @property string $square_logo_url 企业应用方形头像
 * @property string $redirect_domain 企业应用可信域名
 * @property int $isreportenter 是否上报用户进入应用事件。0：不接收；1：接收。
 * @property string $home_url 主页型应用url。url必须以http或者https开头。消息型应用无需该参数
 * @property string $menu 应用菜单
 * @property string $allow_userinfos 企业应用可见范围（人员），其中包括userid
 * @property string $allow_partys 企业应用可见范围（部门）
 * @property string $allow_tags 企业应用可见范围（标签）
 * @property string|null $token 公众号服务器配置：令牌
 * @property string|null $encoding_aes_key 公众号服务器配置：消息加解密密钥
 * @property string|null $access_token
 * @property string|null $expire_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $enabled
 * @property-read Corp $corp
 * @property-read Collection|Message[] $messages
 * @property-read Collection|School[] $schools
 * @method static Builder|App whereAccessToken($value)
 * @method static Builder|App whereAgentid($value)
 * @method static Builder|App whereAllowPartys($value)
 * @method static Builder|App whereAllowTags($value)
 * @method static Builder|App whereAllowUserinfos($value)
 * @method static Builder|App whereCorpId($value)
 * @method static Builder|App whereCreatedAt($value)
 * @method static Builder|App whereEncodingAesKey($value)
 * @method static Builder|App whereToken($value)
 * @method static Builder|App whereDescription($value)
 * @method static Builder|App whereEnabled($value)
 * @method static Builder|App whereExpireAt($value)
 * @method static Builder|App whereHomeUrl($value)
 * @method static Builder|App whereId($value)
 * @method static Builder|App whereIsreportenter($value)
 * @method static Builder|App whereMenu($value)
 * @method static Builder|App whereName($value)
 * @method static Builder|App whereRedirectDomain($value)
 * @method static Builder|App whereReportLocationFlag($value)
 * @method static Builder|App whereSecret($value)
 * @method static Builder|App whereSquareLogoUrl($value)
 * @method static Builder|App whereUpdatedAt($value)
 * @method static Builder|App newModelQuery()
 * @method static Builder|App newQuery()
 * @method static Builder|App query()
 * @mixin Eloquent
 */
class App extends Model {
    
    use ModelTrait;
    
    protected $fillable = [
        'corp_id', 'name', 'agentid', 'secret',
        'token', 'encoding_aes_key', 'menu',
        'description', 'report_location_flag',
        'square_logo_url', 'redirect_domain',
        'isreportenter', 'home_url',
        'allow_userinfos', 'allow_partys',
        'allow_tags', 'access_token',
        'expire_at', 'enabled'
    ];
    
    /**
     * 返回应用所属的企业对象
     *
     * @return BelongsTo
     */
    function corp() { return $this->belongsTo('App\Models\Corp'); }
    
    /**
     * 返回通过指定应用发送/收到的消息
     *
     * @return HasMany
     */
    function messages() { return $this->hasMany('App\Models\Message'); }
    
    /**
     * 返回指定公众号应用对应的所有学校对象
     *
     * @return HasMany
     */
    function schools() { return $this->hasMany('App\Models\School'); }
    
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
                'db' => 'App.token', 'dt' => 2,
                'formatter' => function ($d) {
                    return empty($d) ? '企业应用' : '公众号';
                }
            ],
            [
                'db' => 'Corp.name as corpname', 'dt' => 3,
                'formatter' => function ($d) {
                    return sprintf(Snippet::ICON, 'fa-weixin text-green', '') .
                        '<span class="text-green">' . $d . '</span>';
                }
            ],
            ['db' => 'App.description', 'dt' => 4],
            ['db' => 'App.created_at', 'dt' => 5],
            ['db' => 'App.updated_at', 'dt' => 6],
            [
                'db' => 'App.enabled', 'dt' => 7,
                'formatter' => function ($d, $row) {
                    return Datatable::status($d, $row, false);
                }
            ]
        ];
        $joins = [
            [
                'table' => 'corps',
                'alias' => 'Corp',
                'type'  => 'INNER',
                'conditions' => [
                    'Corp.id = App.corp_id'
                ]
            ]
        ];
        
        return Datatable::simple(
            $this, $columns, $joins,
            'App.corp_id = ' . (new Corp)->corpId()
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
     * @return bool|Collection|Model|null|static|static[]
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
        
        try {
            DB::transaction(function () use ($id) {
                $this->purge(['Message'], 'app_id', 'reset', $id);
                $this->purge(['App'], 'id', 'purge', $id);
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
    
    /**
     * 保存App
     *
     * @param AppRequest $request
     * @return JsonResponse
     * @throws Throwable
     */
    function sync(AppRequest $request) {
        
        $agentid = $request->input('agentid');
        $secret = $request->input('secret');
        $corpId = $request->input('corp_id');
        $app = $this->where('agentid', $agentid)
            ->where('secret', $secret)->first();
        $action = !$app ? 'create' : 'update';
        # 获取应用
        $corpid = Corp::find($app ? $app->corp_id : $corpId)->corpid;
        $token = Wechat::token(
            'ent', $corpid,
            $app ? $app->secret : $secret
        );
        if ($token['errcode']) {
            abort(
                HttpStatusCode::INTERNAL_SERVER_ERROR,
                $token['errmsg']
            );
        }
        $result = json_decode(
            Wechat::invoke(
                'ent', 'agent', 'get',
                [$token['access_token'], $app ? $app->agentid : $agentid]
            )
        );
        abort_if(
            $result->{'errcode'},
            HttpStatusCode::INTERNAL_SERVER_ERROR,
            Constant::WXERR[$result->{'errcode'}]
        );
        # 更新/创建本地应用记录
        $data = [
            'name'                 => $result->{'name'},
            'menu'                 => '0',
            'allow_tags'           => '0',
            'corp_id'              => $corpId,
            'agentid'              => $agentid,
            'secret'               => $secret,
            'description'          => $result->{'description'},
            'report_location_flag' => $result->{'report_location_flag'},
            'square_logo_url'      => $result->{'square_logo_url'},
            'redirect_domain'      => $result->{'redirect_domain'},
            'isreportenter'        => $result->{'isreportenter'},
            'home_url'             => $result->{'home_url'},
            'allow_userinfos'      => json_encode($result->{'allow_userinfos'}),
            'allow_partys'         => json_encode($result->{'allow_partys'}),
            'enabled'              => !$result->{'close'},
        ];
        $app = $app
            ? $this->modify($data, $app->id)->toArray()
            : $this->store($data)->toArray();
        $app['created_at'] = $this->humanDate($app['created_at']);
        $app['updated_at'] = $this->humanDate($app['updated_at']);
        
        return response()->json([
            'app'    => $app,
            'action' => $action,
        ]);
        
    }
    
    /** Helper functions -------------------------------------------------------------------------------------------- */

    
}