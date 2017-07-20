<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use App\Facades\DatatableFacade as Datatable;
use Illuminate\Http\Request;

/**
 * App\Models\App
 *
 * @property int $id
 * @property string $name 应用名称
 * @property string $description 应用备注
 * @property int $agentid 应用id
 * @property string $url 推送请求的访问协议和地址
 * @property string $token 用于生成签名
 * @property string $encodingaeskey 用于消息体的加密，是AES密钥的Base64编码
 * @property int $report_location_flag 企业应用是否打开地理位置上报 0：不上报；1：进入会话上报；2：持续上报
 * @property string $logo_mediaid 企业应用头像的mediaid，通过多媒体接口上传图片获得mediaid，上传后会自动裁剪成方形和圆形两个头像
 * @property string $redirect_domain 企业应用可信域名
 * @property int $isreportuser 是否接收用户变更通知。0：不接收；1：接收。
 * @property int $isreportenter 是否上报用户进入应用事件。0：不接收；1：接收。
 * @property string $home_url 主页型应用url。url必须以http或者https开头。消息型应用无需该参数
 * @property string $chat_extension_url 关联会话url。设置该字段后，企业会话"+"号将出现该应用，点击应用可直接跳转到此url，支持jsapi向当前会话发送消息。
 * @property string $menu 应用菜单
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property int $enabled
 * @method static Builder|App whereAgentid($value)
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
 */
class App extends Model {

    protected $fillable = [
        'name',
        'description',
        'agentid',
        'url',
        'token',
        'encodingaeskey',
        'report_location_flag',
        'logo_mediaid',
        'redirect_domain',
        'isreportuser',
        'isreportenter',
        'home_url',
        'chat_extension_url',
        'menu',
        'enabled'
    ];


    public function datatable(Request $request) {

        $columns = [
            ['db' => 'App.id', 'dt' => 0],
            ['db' => 'App.agentid', 'dt' => 1],
            ['db' => 'App.report_location_flag', 'dt' => 2],
            ['db' => 'App.isreportuser', 'dt' => 3],
            ['db' => 'App.isreportenter', 'dt' => 4],
            ['db' => 'App.created_at', 'dt' => 5],
            ['db' => 'App.updated_at', 'dt' => 6],
            [
                'db' => 'App.enabled', 'dt' => 7,
                'formatter' => function($d, $row) {
                    return Datatable::dtOps($this, $d, $row);
                }
            ]
        ];
        return Datatable::simple($this, $request, $columns);

    }

}
