<?php
namespace App\Models;

use App\Events\AppMenuCreated;
use App\Events\AppMenuUpdated;
use App\Events\AppUpdated;
use App\Facades\DatatableFacade as Datatable;
use App\Facades\Wechat;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;

/**
 * App\Models\App 微信企业应用
 *
 * @property int $id
 * @property string $corp_id 企业id
 * @property string $name 应用名称
 * @property string $description 应用备注
 * @property string $secret 应用密匙
 * @property int $agentid 应用id
 * @property string $url 推送请求的访问协议和地址
 * @property string $token 用于生成签名
 * @property int $report_location_flag 企业应用是否打开地理位置上报 0：不上报；1：进入会话上报；2：持续上报
 * @property int square_logo_url 企业头像url
 * @property string $redirect_domain 企业应用可信域名
 * @property int $isreportenter 是否上报用户进入应用事件。0：不接收；1：接收。
 * @property string $home_url 主页型应用url。url必须以http或者https开头。消息型应用无需该参数
 * @property string $menu 应用菜单
 * @property string $allow_userinfos 企业应用可见范围（人员），其中包括userid
 * @property string $allow_partys 企业应用可见范围（部门）
 * @property string $allow_tags 企业应用可见范围（标签）
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property int $enabled
 * @method static Builder|App whereAgentid($value)
 * @method static Builder|App whereCorpId($value)
 * @method static Builder|App whereChatExtensionUrl($value)
 * @method static Builder|App whereCreatedAt($value)
 * @method static Builder|App whereDescription($value)
 * @method static Builder|App whereEnabled($value)
 * @method static Builder|App whereEncodingaeskey($value)
 * @method static Builder|App whereHomeUrl($value)
 * @method static Builder|App whereId($value)
 * @method static Builder|App whereIsreportenter($value)
 * @method static Builder|App whereIsreportuser($value)
 * @method static Builder|App whereLogoMediaid($value)
 * @method static Builder|App whereMenu($value)
 * @method static Builder|App whereName($value)
 * @method static Builder|App whereRedirectDomain($value)
 * @method static Builder|App whereReportLocationFlag($value)
 * @method static Builder|App whereToken($value)
 * @method static Builder|App whereUpdatedAt($value)
 * @method static Builder|App whereUrl($value)
 * @mixin \Eloquent
 * @property string|null $access_token
 * @property string|null $expire_at
 * @method static Builder|App whereAccessToken($value)
 * @method static Builder|App whereAllowPartys($value)
 * @method static Builder|App whereAllowTags($value)
 * @method static Builder|App whereAllowUserinfos($value)
 * @method static Builder|App whereExpireAt($value)
 * @method static Builder|App whereSecret($value)
 * @method static Builder|App whereSquareLogoUrl($value)
 */
class App extends Model {
    
    protected $fillable = [
        'corp_id','name', 'description', 'agentid',
        'token', 'secret', 'report_location_flag',
        'square_logo_url', 'allow_userinfos',
        'allow_partys', 'allow_tags', 'redirect_domain',
        'isreportenter', 'home_url', 'menu',
        'access_token', 'expire_at', 'enabled',
    ];
    public function store() {
        
        $secret = Request::input('secret');
        $agentid = Request::input('agentid');
        $corp_id = Request::input('corp_id');
    
        $corp = new Corp();
        $corps = $corp::whereName('万浪软件')->first();
        $corpId = $corps->corpid;
        $dir = dirname(__FILE__);
        $path = substr($dir, 0, stripos($dir, 'app/Jobs'));
        $tokenFile = $path . 'public/token.txt';
        $token = Wechat::getAccessToken($tokenFile, $corpId, $secret);
    
        $corpApp = json_decode(Wechat::getApp($token, $agentid));
        // dd($corp_id);
        $app = $this::whereAgentid($agentid)->where('corp_id', $corp_id)->first();
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
            $a = $this->create($data);
            
            $response = response()->json(['app' => $this->formatDateTime($a->toArray()) , 'action' => 'create']);
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
            $app->enabled = $corpApp->close;
            $app->save();
            $response = response()->json(['app' => $this->formatDateTime($app->toArray()) , 'action' => 'update']);
        }
        
        return $response;
        
    }
    /**
     * 更新App
     *
     * @param array $data
     * @param $id
     * @return bool|\Illuminate\Database\Eloquent\Collection|Model|null|static|static[]
     */
    public function modify(array $data, $id) {
        
        $app = $this->find($id);
        if (!$app) { return false; }
        $updated = $this->update($data);
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
     * @return bool|\Illuminate\Database\Eloquent\Collection|Model|null|static|static[]
     */
    public function storeMenu(array $data, $id) {
    
        $app = $this->find($id);
        if (!$app) { return false; }
        $updated = $this->update($data);
        if ($updated) {
            event(new AppMenuUpdated($app));
            return $app;
        }
        return $updated ? $app : false;
    
    }
    
    public function datatable() {
        
        $columns = [
            ['db' => 'App.id', 'dt' => 0],
            ['db' => 'App.name', 'dt' => 1],
            ['db' => 'App.agentid', 'dt' => 2],
            ['db' => 'App.report_location_flag', 'dt' => 3],
            ['db' => 'App.isreportuser', 'dt' => 4],
            ['db' => 'App.isreportenter', 'dt' => 5],
            ['db' => 'App.created_at', 'dt' => 6],
            ['db' => 'App.updated_at', 'dt' => 7],
            [
                'db'        => 'App.enabled', 'dt' => 8,
                'formatter' => function ($d, $row) {
                    return Datatable::dtOps($this, $d, $row);
                },
            ],
        ];
        
        return Datatable::simple($this, $columns);
        
    }
    
    /**
     *  将对象转换为数组
     *
     * @param $obj
     * @return array|void
     */
    public function object_to_array($obj) {
        
        $obj = (array)$obj;
        foreach ($obj as $k => $v) {
            if (gettype($v) == 'resource') {
                return;
            }
            if (gettype($v) == 'object' || gettype($v) == 'array') {
                $obj[$k] = (array)$this->object_to_array($v);
            }
        }
    
        return $obj;
        
    }
    
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
