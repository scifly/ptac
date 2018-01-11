<?php

namespace App\Models;

use App\Events\AppMenuUpdated;
use App\Events\AppUpdated;
use App\Facades\Wechat;
use Carbon\Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;

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
     * 保存App
     *
     * @return \Illuminate\Http\JsonResponse
     */
    static function store() {

        $secret = Request::input('secret');
        $agentid = Request::input('agentid');
        $corp_id = Corp::corpId();
        $corpid = Corp::find($corp_id)->corpid;
        $token = Wechat::getAccessToken($corpid, $secret);
        $corpApp = json_decode(Wechat::getApp($token, $agentid));
        if (isset($corpApp->name)) {
            $app = self::whereAgentid($agentid)->where('corp_id', $corp_id)->first();
            if (!$app) {
                $data = [
                    'corp_id' => intval($corp_id),
                    'name' => $corpApp->name,
                    'secret' => $secret,
                    'description' => $corpApp->description,
                    'agentid' => $agentid,
                    'report_location_flag' => $corpApp->report_location_flag,
                    'square_logo_url' => $corpApp->square_logo_url,
                    'redirect_domain' => $corpApp->redirect_domain,
                    'isreportenter' => $corpApp->isreportenter,
                    'home_url' => $corpApp->home_url,
                    'menu' => '',
                    'allow_userinfos' => json_encode($corpApp->allow_userinfos),
                    'allow_partys' => json_encode($corpApp->allow_partys),
                    'allow_tags' => isset($corpApp->allow_tags) ? json_encode($corpApp->allow_tags) : '',
                    'enabled' => $corpApp->close,
                ];
                $a = self::create($data);
                $response = response()->json(['app' => self::formatDateTime($a->toArray()), 'action' => 'create']);
            } else {
                $app->corp_id = intval($corp_id);
                $app->name = $corpApp->name;
                $app->secret = $secret;
                $app->description = $corpApp->description;
                $app->agentid = $agentid;
                $app->report_location_flag = $corpApp->report_location_flag;
                $app->square_logo_url = $corpApp->square_logo_url;
                $app->redirect_domain = $corpApp->redirect_domain;
                $app->isreportenter = $corpApp->isreportenter;
                $app->home_url = $corpApp->home_url;
                $app->menu = '';
                $app->allow_userinfos = json_encode($corpApp->allow_userinfos);
                $app->allow_partys = json_encode($corpApp->allow_partys);
                $app->allow_tags = isset($corpApp->allow_tags) ? json_encode($corpApp->allow_tags) : '';
                $app->enabled = !($corpApp->close);
                $app->save();
                $app = $app->toArray();
                self::formatDateTime($app);
                $response = response()->json(['app' => $app, 'action' => 'update']);
            }
        } else {
            $response = response()->json(['error' => $corpApp->errmsg]);
        }

        return $response;

    }

    /**
     * 更新App
     *
     * @param array $data
     * @param $id
     * @return bool|Collection|Model|null|static|static[]
     */
    static function modify(array $data, $id) {

        $app = self::find($id);
        if (!$app) { return false; }
        $updated = $app->update($data);
        if ($updated) {
            event(new AppUpdated($app));
            return $app;
        }
        return $updated ? $app : false;

    }

    /**
     * 同步菜单
     *
     * @param array $data
     * @param $id
     * @return bool|Collection|Model|null|static|static[]
     */
    static function storeMenu(array $data, $id) {

        $app = self::find($id);
        if (!$app) { return false; }
        $updated = $app->update($data);
        if ($updated) {
            event(new AppMenuUpdated($app));
            return $app;
        }
        
        return $updated ? $app : false;

    }
    
    /**
     * 将日期时间转换为人类友好的格式
     *
     * @param $app
     */
    private static function formatDateTime(&$app) {
        
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
