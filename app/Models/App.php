<?php
namespace App\Models;

use App\Facades\Wechat;
use App\Helpers\{Constant, HttpStatusCode, ModelTrait};
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
 * @property string|null $access_token
 * @property string|null $expire_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $enabled
 * @property-read Corp $corp
 * @property-read Collection|Message[] $messages
 * @method static Builder|App whereAccessToken($value)
 * @method static Builder|App whereAgentid($value)
 * @method static Builder|App whereAllowPartys($value)
 * @method static Builder|App whereAllowTags($value)
 * @method static Builder|App whereAllowUserinfos($value)
 * @method static Builder|App whereCorpId($value)
 * @method static Builder|App whereCreatedAt($value)
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
        'corp_id', 'name', 'description', 'agentid',
        'token', 'secret', 'report_location_flag',
        'square_logo_url', 'allow_userinfos',
        'allow_partys', 'allow_tags', 'redirect_domain',
        'isreportenter', 'home_url', 'menu',
        'access_token', 'expire_at', 'enabled',
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
     * 返回指定企业对应的应用列表
     *
     * @param AppRequest $request
     * @return JsonResponse
     */
    function index(AppRequest $request) {
        
        $corpId = $request->query('corpId');
        $apps = App::whereCorpId($corpId)->get()->toArray();
        if (empty($apps)) {
            return response()->json([
                'apps' => '<tr id="na"><td colspan="8" style="text-align: center;">( n/a )</td></tr>',
            ]);
        }
        /** @noinspection HtmlUnknownTarget */
        $tr =
            '<tr id="app%s">
                <td>%s</td>
                <td class="text-center">%s</td>
                <td class="text-center">%s</td>
                <td class="text-center"><img class="img-circle" style="height: 16px;" src="%s" alt="" /></td>
                <td class="text-center">%s</td>
                <td class="text-center">%s</td>
                <td class="text-center">%s</td>
                <td class="text-right">
                    %s
                    &nbsp;&nbsp;&nbsp;
                    <a href="#"><i class="fa fa-pencil" title="修改"></i></a>
                    &nbsp;&nbsp;
                    <a href="#"><i class="fa fa-remove text-red" title="删除"</a>
                </td>
            </tr>';
        $html = '';
        foreach ($apps as $app) {
            $html .= sprintf(
                $tr,
                $app['agentid'],
                $app['id'],
                $app['agentid'],
                $app['name'],
                $app['square_logo_url'],
                $app['secret'],
                $this->humanDate($app['created_at']),
                $this->humanDate($app['updated_at']),
                $app['enabled']
                    ? '<i class="fa fa-circle text-green" title="已启用"></i>'
                    : '<i class="fa fa-circle text-gray" title="未启用"></i>'
            );
        }
        
        return response()->json([
            'apps' => $html,
        ]);
        
    }
    
    /**
     * 保存App
     *
     * @param AppRequest $request
     * @return JsonResponse
     * @throws Exception
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
        $token = Wechat::getAccessToken($corpid, $app ? $app->secret : $secret);
        if ($token['errcode']) {
            abort(
                HttpStatusCode::INTERNAL_SERVER_ERROR,
                $token['errmsg']
            );
        }
        $result = json_decode(Wechat::getApp($token['access_token'], $app ? $app->agentid : $agentid));
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
    
    /**
     * 更新App
     *
     * @param array $data
     * @param $id
     * @return bool|Collection|Model|null|static|static[]
     */
    function modify(array $data, $id) {
        
        $app = $this->find($id);
        $updated = $app->update($data);
        if ($updated) {
            SyncApp::dispatch($this->find($id), Auth::id());
        }
        
        return $updated ? $this->find($id) : false;
        
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
    
    /** Helper functions -------------------------------------------------------------------------------------------- */
    /**
     * 保存新创建的app
     *
     * @param array $data
     * @return $this|bool|Model
     */
    private function store(array $data) {
        
        return $this->create($data) ?? false;
        
    }
    
}