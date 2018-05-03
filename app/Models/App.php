<?php
namespace App\Models;

use Eloquent;
use Carbon\Carbon;
use App\Facades\Wechat;
use App\Helpers\HttpStatusCode;
use App\Http\Requests\AppRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

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
 * @mixin Eloquent
 * @property-read Corp $corp
 */
class App extends Model {

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
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    function corp() {
        
        return $this->belongsTo('App\Models\Corp');
        
    }
    
    /**
     * 保存App
     *
     * @param AppRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    function sync(AppRequest $request) {

        $agentid = Request::input('agentid');
        $secret = Request::input('secret');
        $app = $this->where('agentid', $agentid)
            ->where('secret', $secret)->first();
        if (!$app) {
            $app = $this->store($request->all());
            $action = 'create';
        } else {
            $action = 'update';
        }
        abort_if(
            !$app,
            HttpStatusCode::NOT_FOUND,
            __('messages.not_found')
        );
        $corpid = Corp::find($app->corp_id)->corpid;
        $token = Wechat::getAccessToken($corpid, $app->secret);
        $result = json_decode(Wechat::getApp($token, $app->agentid));
        
        abort_if(
            $result->{'errcode'} != 0,
            HttpStatusCode::INTERNAL_SERVER_ERROR,
            $result->{'errcode'} . ': ' . $result->{'errmsg'}
        );
        $data = [
            'name' => $result->{'name'},
            'description' => $result->{'description'},
            'report_location_flag' => $result->{'report_location_flag'},
            'square_logo_url' => $result->{'square_logo_url'},
            'redirect_domain' => $result->{'redirect_domain'},
            'isreportenter' => $result->{'isreportenter'},
            'home_url' => $result->{'home_url'},
            'allow_user_infos' => $result->{'allow_user_infos'},
            'allow_partys' => $result->{'allow_partys'},
            'allow_tags' => $result->{'allow_tags'},
            'enabled' => $result->{'close'}
        ];
        $app = $this->modify($data, $app->id)->toArray();
        $this->formatDateTime($app);
        
        return response()->json([
            'app' => $app,
            'action' => $action,
        ]);

    }
    
    /**
     * 保存新创建的app
     *
     * @param array $data
     * @return $this|bool|Model
     */
    private function store(array $data) {
        
        return $this->create($data) ?? false;
        
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
        $app->update($data);

        return $this->find($id) ?? false;

    }
    
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
                'apps' => '<tr id="na"><td colspan="8" style="text-align: center;">( n/a )</td></tr>'
            ]);
        }
        $tr =
            '<tr id="app%s">
                <td>%s</td>
                <td class="text-center">%s</td>
                <td class="text-center">%s</td>
                <td class="text-center"><img style="width: 16px; height: 16px;" src="%s"/></td>
                <td>%s</td>
                <td class="text-center">%s</td>
                <td class="text-center">%s</td>
                <td class="text-right">
                    %s
                    &nbsp;&nbsp;
                    <a href="#"><i class="fa fa-pencil" title="修改"></i></a>
                    &nbsp;&nbsp;
                    <a href="#"><i class="fa fa-exchange" title="同步菜单"></i></a>
                </td>
            </tr>';
        $html = '';
        foreach ($apps as $app) {
            $this->formatDateTime($app);
            $html .= sprintf(
                $tr,
                $app['agentid'],
                $app['id'],
                $app['agentid'],
                $app['name'],
                $app['square_logo_url'],
                $app['description'],
                $app['created_at'],
                $app['updated_at'],
                $app['enabled']
                    ? '<i class="fa fa-circle text-green" title="已启用"></i>'
                    : '<i class="fa fa-circle text-gray" title="未启用"></i>'
            );
        }
        
        return response()->json([
            'apps' => $html
        ]);
        
    }
    
    /**
     * 将日期时间转换为人类友好的格式
     *
     * @param $app
     */
    private function formatDateTime(&$app) {
        
        Carbon::setLocale('zh');
        if ($app['created_at']) {
            $dt = Carbon::createFromFormat('Y-m-d H:i:s', $app['created_at']);
            $app['created_at'] = $dt->diffForHumans();
        }
        if ($app['updated_at']) {
            $dt = Carbon::createFromFormat('Y-m-d H:i:s', $app['updated_at']);
            $app['updated_at'] = $dt->diffForHumans();
        }
        
    }
    
    
}
