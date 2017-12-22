<?php
namespace App\Facades;

use App\Models\App;
use App\Models\Corp;
use Illuminate\Support\Facades\Facade;
use Illuminate\Support\Facades\Log;

class Wechat extends Facade {
    
    # 应用授权作用域
    const SCOPE_BASE = 'snsapi_base';               # 自动授权，可获取成员的基础信息；
    const SCOPE_USERINFO = 'snsapi_userinfo';       # 自动授权，可获取不包括用户手机和邮箱的详细信息
    const SCOPE_PRIVATEINFO = 'snsapi_privateinfo'; # 需用户手动授权，可获取成员的详细信息（包括手机和邮箱地址）
    
    # 获取access_token - https://work.weixin.qq.com/api/doc#10013
    const URL_GET_ACCESSTOKEN = 'https://qyapi.weixin.qq.com/cgi-bin/gettoken?corpid=%s&corpsecret=%s';
    
    /** 认证接口 */
    # 身份验证 - OAuth验证接口
    # 获取企业code的GET请求地址
    const URL_GET_CODE = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=%s&redirect_uri=%s' .
    '&response_type=code&scope=%s&agentid=%s&state=getcodeblahblah#wechat_redirect';
    # 根据code获取用户信息的GET请求地址
    const URL_GET_USERINFO = 'https://qyapi.weixin.qq.com/cgi-bin/user/getuserinfo?access_token=%s&code=%s';
    # 使用user_ticket获取成员详情，请求方式: POST
    const URL_GET_USERDETAIL = 'https://qyapi.weixin.qq.com/cgi-bin/user/getuserdetail?access_token=%s';
    # 身份验证 - userid与openid互换接口
    # userid转换成openid接口
    const URL_USERID_TO_OPENID = 'https://qyapi.weixin.qq.com/cgi-bin/user/convert_to_openid?access_token=%s';
    # openid转换成userid接口
    const URL_OPENID_TO_USERID = 'https://qyapi.weixin.qq.com/cgi-bin/user/convert_to_userid?access_token=%s';
    # 成员登录授权 - 获取企业号登录用户信息
    const URL_GET_LOGIN_INFO = 'https://qyapi.weixin.qq.com/cgi-bin/service/get_login_info?access_token=%s';
    # 单点登录授权 - 获取登录企业号官网的url
    const URL_GET_LOGIN_URL = 'https://qyapi.weixin.qq.com/cgi-bin/service/get_login_url?access_token=%s';
    
    /** 成员管理 */
    # 创建成员(POST) - https://work.weixin.qq.com/api/doc#10018
    const URL_CREATE_USER = 'https://qyapi.weixin.qq.com/cgi-bin/user/create?access_token=%s';
    # 读取成员(GET) - https://work.weixin.qq.com/api/doc#10019
    const URL_GET_USER = 'https://qyapi.weixin.qq.com/cgi-bin/user/get?access_token=%s&userid=%s';
    # 更新成员(POST) - https://work.weixin.qq.com/api/doc#10020
    const URL_UPDATE_USER = 'https://qyapi.weixin.qq.com/cgi-bin/user/update?access_token=%s';
    # 删除成员(GET) - https://work.weixin.qq.com/api/doc#10030
    const URL_DEL_USER = 'https://qyapi.weixin.qq.com/cgi-bin/user/delete?access_token=%s&userid=%s';
    # 批量删除成员(POST) - https://work.weixin.qq.com/api/doc#10060
    const URL_BATCH_DEL_USER = 'https://qyapi.weixin.qq.com/cgi-bin/user/batchdelete?access_token=%s';
    # 获取部门成员(GET) - https://work.weixin.qq.com/api/doc#10061
    const URL_GET_DEPT_USER = 'https://qyapi.weixin.qq.com/cgi-bin/user/simplelist?access_token=%s&department_id=%s&fetch_child=%s';
    # 获取部门成员详情(GET) - https://work.weixin.qq.com/api/doc#10063
    const URL_GET_DEPT_USER_DETAIL = 'https://qyapi.weixin.qq.com/cgi-bin/user/list?access_token=%s&department_id=%s&fetch_child=%s';
    
    /** 部门管理 */
    # 创建部门(POST) - https://work.weixin.qq.com/api/doc#10076
    const URL_CREATE_DEPT = 'https://qyapi.weixin.qq.com/cgi-bin/department/create?access_token=%s';
    # 更新部门(POST) - https://work.weixin.qq.com/api/doc#10077
    const URL_UPDATE_DEPT = 'https://qyapi.weixin.qq.com/cgi-bin/department/update?access_token=%s';
    # 删除部门(GET) - https://work.weixin.qq.com/api/doc#10079
    const URL_DEL_DEPT = 'https://qyapi.weixin.qq.com/cgi-bin/department/delete?access_token=%s&id=%s';
    # 获取部门列表(GET) - https://work.weixin.qq.com/api/doc#10093
    const URL_GET_DEPT_LIST = 'https://qyapi.weixin.qq.com/cgi-bin/department/list?access_token=%s&id=%s';
    
    /** 异步任务接口 - https://work.weixin.qq.com/api/doc#10138 */
    # 通讯录更新 - 增量更新成员(POST)
    const URL_INCREMENTAL_UPDATE_USER = 'https://qyapi.weixin.qq.com/cgi-bin/batch/syncuser?access_token=%s';
    # 通讯录更新 - 全量覆盖成员(POST)
    const URL_OVERRIDE_UPDATE_USER = 'https://qyapi.weixin.qq.com/cgi-bin/batch/replaceuser?access_token=%s';
    # 通讯录更新 - 全量覆盖部门(POST)
    const URL_OVERRIDE_UPDATE_DEPT = 'https://qyapi.weixin.qq.com/cgi-bin/batch/replaceparty?access_token=%s';
    # 获取异步任务结果(GET)
    const URL_GET_ASYNC_RESULT = 'https://qyapi.weixin.qq.com/cgi-bin/batch/getresult?access_token=%s&jobid=%s';
    
    /** 标签管理 */
    # 创建标签(POST) - https://work.weixin.qq.com/api/doc#10915
    const URL_CREATE_TAG = 'https://qyapi.weixin.qq.com/cgi-bin/tag/create?access_token=%s';
    # 更新标签名字(POST) - https://work.weixin.qq.com/api/doc#10919
    const URL_UPDATE_TAG = 'https://qyapi.weixin.qq.com/cgi-bin/tag/update?access_token=%s';
    # 删除标签(GET) - https://work.weixin.qq.com/api/doc#10920
    const URL_DEL_TAG = 'https://qyapi.weixin.qq.com/cgi-bin/tag/delete?access_token=%s&tagid=%s';
    # 获取标签成员(GET) - https://work.weixin.qq.com/api/doc#10921
    const URL_GET_TAG_USER = 'https://qyapi.weixin.qq.com/cgi-bin/tag/get?access_token=%s&tagid=%s';
    # 增加标签成员(GET) - https://work.weixin.qq.com/api/doc#10923
    const URL_ADD_TAG_USER = 'https://qyapi.weixin.qq.com/cgi-bin/tag/addtagusers?access_token=%s';
    # 删除标签成员(POST) - https://work.weixin.qq.com/api/doc#10925
    const URL_DEL_TAG_USER = 'https://qyapi.weixin.qq.com/cgi-bin/tag/deltagusers?access_token=%s';
    # 获取标签列表(GET) - https://work.weixin.qq.com/api/doc#10926
    const URL_GET_TAG_LIST = 'https://qyapi.weixin.qq.com/cgi-bin/tag/list?access_token=%s';
    
    /** 应用管理 - https://work.weixin.qq.com/api/doc#10025 */
    # 获取应用(GET) - https://work.weixin.qq.com/api/doc#10087
    const URL_GET_APP = 'https://qyapi.weixin.qq.com/cgi-bin/agent/get?access_token=%s&agentid=%s';
    # 设置应用(POST) - https://work.weixin.qq.com/api/doc#10088
    const URL_CONFIG_APP = 'https://qyapi.weixin.qq.com/cgi-bin/agent/set?access_token=%s';
    # 获取应用列表 - https://work.weixin.qq.com/api/doc#11214
    const URL_APP_LIST = 'https://qyapi.weixin.qq.com/cgi-bin/agent/list?access_token=%s';
    
    /** 消息推送 */
    # 发送消息(POST) - https://work.weixin.qq.com/api/doc#10167
    const URL_SEND_MESSAGE = 'https://qyapi.weixin.qq.com/cgi-bin/message/send?access_token=%s';
    # 接收消息服务器配置 - https://work.weixin.qq.com/api/doc#10514
    # 接收事件推送 - https://work.weixin.qq.com/api/doc#10427
    # 接收普通消息 - https://work.weixin.qq.com/api/doc#10426
    # 被动回复消息 - https://work.weixin.qq.com/api/doc#10428
    /** 自定义菜单 */
    # 创建菜单(POST) - https://work.weixin.qq.com/api/doc#10786
    const URL_CREATE_MENU = 'https://qyapi.weixin.qq.com/cgi-bin/menu/create?access_token=%s&agentid=%s';
    # 获取菜单(GET) - https://work.weixin.qq.com/api/doc#10787
    const URL_GET_MENU = 'https://qyapi.weixin.qq.com/cgi-bin/menu/get?access_token=%s&agentid=%s';
    # 删除菜单(GET) - https://work.weixin.qq.com/api/doc#10788
    const URL_DEL_MENU = 'https://qyapi.weixin.qq.com/cgi-bin/menu/delete?access_token=%s&agentid=%s';
    
    /** 素材管理 */
    # 上传临时素材文件(POST) - https://work.weixin.qq.com/api/doc#10112
    const URL_UPLOAD_MEDIA = 'https://qyapi.weixin.qq.com/cgi-bin/media/upload?access_token=%s&type=%s';
    # 获取临时素材文件(GET) - https://work.weixin.qq.com/api/doc#10115
    const URL_GET_MEDIA = 'https://qyapi.weixin.qq.com/cgi-bin/media/get?access_token=%s&media_id=%s';
    
    /** 短信发送 */
    # 账号激活
    const URL_ACTIVATE_ACCOUNT = "http://sdk2.028lk.com:9880/sdk2/UpdReg.aspx?CorpID=%s&Pwd=%s&CorpName=%s&LinkMan=%s&Tel=%s&Mobile=%s&Email=%s&Memo=%s";
    # 更改密码 UpdPwd 地址
    const URL_UPDATE_PASSWORD = "http://sdk2.028lk.com:9880/sdk2/UpdPwd.aspx?CorpID=%s&Pwd=%s&NewPwd=%s";
    # 查询余额 大于等于30s调用一次，过快返回-101
    const URL_GET_BALANCE = "http://sdk2.028lk.com:9880/sdk2/SelSum.aspx?CorpID=%s&Pwd=%s";
    # (批量)发送短信 BatchSend2地址
    const URL_BATCH_SEND_SMS = "http://sdk2.028lk.com:9880/sdk2/BatchSend2.aspx?CorpID=%s&Pwd=%s&Mobile=%s&Content=%s&Cell=%s&SendTime=%s";
    # 接收短信回复
    const URL_GET_RESPONSE_SMS = "http://sdk2.028lk.com:9880/sdk2/Get.aspx?CorpID=%s&Pwd=%s";
    
    
    /**
     * 获取access_token
     *
     * @param string $corpId 企业号ID
     * @param string $secret 应用secret
     * @return bool|mixed
     */
    static function getAccessToken($corpId, $secret) {
        $app = App::whereSecret($secret)->first();
        if ($app['expire_at'] < time() || !isset($app['expire_at'])) {
            $token = self::curlGet(sprintf(self::URL_GET_ACCESSTOKEN, $corpId, $secret));
            $result = json_decode($token);

            if ($result) {
                $accessToken = $result->{'access_token'};
                $app->update([
                    'expire_at' => date('Y-m-d H:i:s', time() + 7000),
                    'access_token' => $accessToken
                ]);
            } else {
                return false;
            }
        } else {
            $accessToken = $app['access_token'];
        }
        Log::debug('token');
        return $accessToken;
        
    }
    
    static function curlGet($url) {
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
        
    }
    
    /**
     * 生成“获取Code”的链接地址，并返回封装后的页面跳转javascript脚本
     *
     * @param string $corpId 企业号ID
     * @param integer $agentId 企业应用ID
     * @param string $redirectUri 授权后重定向的回调链接地址
     * @return string 返回回调地址跳转javascript脚本字符串
     */
    static function getCodeUrl($corpId, $agentId, $redirectUri) {
        
        $url = sprintf(
            self::URL_GET_CODE,
            $corpId,
            urlencode($redirectUri),
            self::SCOPE_USERINFO,
            $agentId
        );

        return "window.location = \"{$url}\"";
        
    }
    
    /**
     * 根据access_token和code获取用户详细信息（除电话和邮箱外）
     *
     * @param string $accessToken 接口调用凭证
     * @param string $code 通过成员授权获取到的code，每次成员授权带上的code将不一样，code只能使用一次，10分钟未被使用自动过期
     * @return mixed
     */
    static function getUserInfo($accessToken, $code) {

        return self::curlGet(sprintf(
            self::URL_GET_USERINFO,
            $accessToken,
            $code
        ));
        
    }
    
    /**
     * 根据access_token及user_ticket获取用户详细信息
     *
     * @param string $accessToken 接口调用凭证
     * @return mixed
     */
    static function getUserDetail($accessToken) {
        
        return self::curlPost(
            sprintf(self::URL_GET_USERDETAIL, $accessToken),
            json_encode(['user_ticket' => 'USER_TICKET'])
        );
        
    }
    
    static function curlPost($url, $post = '') {
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
        
    }
    
    /**
     * 根据access_token, userid和agentid将userid转换成openid
     *
     * @param string $accessToken 接口调用凭证
     * @param string $userId 微信用户ID
     * @param integer $agentId 企业应用ID
     * @return mixed
     */
    static function convertToOpenid($accessToken, $userId, $agentId) {
        
        return self::curlPost(
            sprintf(self::URL_USERID_TO_OPENID, $accessToken),
            json_encode(['userid' => $userId, 'agentid' => $agentId])
        );
        
    }
    
    /**
     * 根据access_token和用户的openid将openid转换成userid
     *
     * @param $accessToken
     * @param $openid
     * @return mixed
     */
    static function convertToUserId($accessToken, $openid) {
        
        return self::curlPost(
            sprintf(self::URL_OPENID_TO_USERID, $accessToken),
            json_encode(['openid' => $openid])
        );
        
    }
    
    /**
     * 根据access_token和code获取企业号登录用户信息
     *
     * @param string $accessToken
     * @param string $authCode oauth2.0授权企业号管理员登录产生的code，只能使用一次，10分钟未被使用自动过期
     * @return mixed
     */
    static function getLoginInfo($accessToken, $authCode) {
        
        return self::curlPost(
            sprintf(self::URL_GET_LOGIN_INFO, $accessToken),
            json_encode(['auth_code' => $authCode])
        );
        
    }
    
    /**
     * 根据access_token, login_ticket，target或agentid获取登录企业号官网的url
     *
     * @param string $accessToken
     * @param string $loginTicket 通过get_login_info得到的login_ticket, 10小时有效
     * @param string $target 登录跳转到企业号后台的目标页面，目前有：agent_setting、send_msg、contact
     * @param null $agentId 授权方应用id
     * @return mixed
     */
    static function getLoginUrl($accessToken, $loginTicket, $target, $agentId = null) {
        
        return self::curlPost(
            sprintf(self::URL_GET_LOGIN_URL, $accessToken),
            json_encode([
                'login_ticket' => $loginTicket,
                'target'       => $target,
                'agentid'      => $agentId,
            ])
        );
        
    }
    
    /**
     * 成员管理 - 创建成员
     *
     * @param string $accessToken 接口调用凭证
     * @param array $data
     * @return mixed json格式
     * @internal param string $userId 用户UserID。对应管理端的帐号，企业内必须唯一。不区分大小写，长度为1~64个字节
     * @internal param string $name 用户名称。长度为1~64个字节
     * @internal param string $englishName 英文名。长度为1-64个字节。第三方暂不支持
     * @internal param string $mobile 手机号码。企业内必须唯一
     * @internal param array $department 成员所属部门id列表,不超过20个
     * @internal param array $order 部门内的排序值，默认为0。数量必须和department一致，数值越大排序越前面。第三方暂不支持
     * @internal param string $position 职位信息。长度为0~64个字节
     * @internal param bool $gender 性别。1表示男性，2表示女性
     * @internal param string $email 邮箱。长度为0~64个字节。企业内必须唯一
     * @internal param bool $isLeader 上级字段，标识是否为上级。第三方暂不支持
     * @internal param bool $enable 启用/禁用成员。1表示启用成员，0表示禁用成员
     * @internal param string $avatarMediaId 成员头像的mediaid，通过多媒体接口上传图片获得的mediaid
     * @internal param string $telephone 座机。长度0-64个字节。第三方暂不支持
     * @internal param array $extAttr 自定义字段。自定义字段需要先在WEB管理端“我的企业” — “通讯录管理”添加，否则忽略未知属性的赋值
     */
    static function createUser(
        
        $accessToken, array $data
    ) {
        Log::debug(json_encode($data));
        return self::curlPost(
            sprintf(self::URL_CREATE_USER, $accessToken),
            json_encode($data)
        );
        
    }
    
    /**
     * 成员管理 - 读取成员
     *
     * @param string $accessToken 接口调用凭证
     * @param string $userId 成员UserID。对应管理端的帐号，企业内必须唯一。不区分大小写，长度为1~64个字节
     * @return mixed
     */
    static function getUser($accessToken, $userId) {
        return self::curlGet(sprintf(self::URL_GET_USER, $accessToken, $userId));
        
    }
    
    /**
     * 成员管理 - 更新成员
     *
     * @param string $accessToken 接口调用凭证
     * @param array $data
     * @return mixed json格式
     * @internal param string $userId 用户UserID。对应管理端的帐号，企业内必须唯一。不区分大小写，长度为1~64个字节
     * @internal param string $name 用户名称。长度为1~64个字节
     * @internal param string $englishName 英文名。长度为1-64个字节。第三方暂不支持
     * @internal param string $mobile 手机号码。企业内必须唯一
     * @internal param array $department 成员所属部门id列表,不超过20个
     * @internal param array $order 部门内的排序值，默认为0。数量必须和department一致，数值越大排序越前面。第三方暂不支持
     * @internal param string $position 职位信息。长度为0~64个字节
     * @internal param bool $gender 性别。1表示男性，2表示女性
     * @internal param string $email 邮箱。长度为0~64个字节。企业内必须唯一
     * @internal param bool $isLeader 上级字段，标识是否为上级。第三方暂不支持
     * @internal param bool $enable 启用/禁用成员。1表示启用成员，0表示禁用成员
     * @internal param string $avatarMediaId 成员头像的mediaid，通过多媒体接口上传图片获得的mediaid
     * @internal param string $telephone 座机。长度0-64个字节。第三方暂不支持
     * @internal param array $extAttr 自定义字段。自定义字段需要先在WEB管理端“我的企业” — “通讯录管理”添加，否则忽略未知属性的赋值
     */
    static function updateUser(
        $accessToken, array $data
    ) {
        
        return self::curlPost(
            sprintf(self::URL_UPDATE_USER, $accessToken),
            json_encode($data)
        );
        
    }
    
    /**
     * 成员管理 - 删除成员
     *
     * @param string $accessToken 接口调用凭证
     * @param string $userId 成员UserID，对应管理端的帐号
     * @return mixed
     */
    static function delUser($accessToken, $userId) {
        
        return self::curlGet(sprintf(self::URL_DEL_USER, $accessToken, $userId));
        
    }
    
    /**
     * 成员管理 - 批量删除成员
     *
     * @param string $accessToken 接口调用凭证
     * @param array $userIdList 成员UserID列表。对应管理端的帐号
     * @return mixed json格式
     */
    static function batchDelUser($accessToken, $userIdList) {
        
        return self::curlPost(
            sprintf(self::URL_BATCH_DEL_USER, $accessToken),
            json_encode($userIdList)
        );
        
    }
    
    /**
     * 成员管理 - 获取部门成员
     *
     * @param string $accessToken 接口调用凭证
     * @param integer $departmentId 获取的部门ID
     * @param boolean $fetchChild 1/0：是否递归获取子部门下面的成员
     * @return mixed json格式
     */
    static function getDeptUser($accessToken, $departmentId, $fetchChild = null) {
        
        return self::curlGet(sprintf(self::URL_GET_DEPT_USER, $accessToken, $departmentId, $fetchChild));
        
    }
    
    /**
     * 成员管理 - 获取部门成员详情
     *
     * @param string $accessToken 接口调用凭证
     * @param integer $departmentId 获取的部门ID
     * @param bool $fetch_child 1/0：是否递归获取子部门下面的成员
     * @return mixed json格式
     */
    static function getDeptUserDetail($accessToken, $departmentId, $fetch_child = null) {
        
        return self::curlGet(sprintf(self::URL_GET_DEPT_USER_DETAIL, $accessToken, $departmentId, $fetch_child));
        
    }
    
    /**
     * 部门管理 - 创建部门
     *
     * @param string $accessToken 接口调用凭证
     * @param string $name 部门名称。长度限制为1~64个字节，字符不能包括\:?”<>｜
     * @param integer $parentId 父部门id
     * @param integer $order 在父部门中的次序值，值越大排序越靠前。
     * @param integer $id 部门id。指定时必须大于1，否则自动生成
     * @return mixed
     */
    static function createDept($accessToken, $name, $parentId, $order = null, $id = null) {
        
        return self::curlPost(
            sprintf(self::URL_CREATE_DEPT, $accessToken),
            json_encode([
                'name'     => $name,
                'parentid' => $parentId,
                'order'    => $order,
                'id'       => $id,
            ])
        );
    }
    
    /**
     * 部门管理 - 更新部门
     *
     * @param string $accessToken 接口调用凭证
     * @param integer $id 部门id
     * @param string $name 部门名称。长度限制为1~64个字节，不得包括\:?”<>｜
     * @param integer $parentId 父部门id
     * @param integer $order 在父部门中的次序值， 值越大的排序越靠前。
     * @return mixed {"errcode": 0, "errmsg": "updated"}
     */
    static function updateDept($accessToken, $id, $name = null, $parentId = null, $order = null) {
        
        return self::curlPost(
            sprintf(self::URL_UPDATE_DEPT, $accessToken),
            json_encode([
                'id'       => $id,
                'name'     => $name,
                'parentid' => $parentId,
                'order'    => $order,
            ])
        );
        
    }
    
    /**
     * 部门管理 - 删除部门
     *
     * @param string $accessToken 接口调用凭证
     * @param integer $id 部门id。（注：不能删除根部门；不能删除含有子部门、成员的部门）
     * @return mixed {"errcode": 0, "errmsg": "updated"}
     */
    static function delDept($accessToken, $id) {
        
        return self::curlGet(sprintf(self::URL_DEL_DEPT, $accessToken, $id));
        
    }
    
    /**
     * 部门管理 - 获取部门列表
     *
     * @param string $accessToken 接口调用凭证
     * @param integer $id 部门id。获取指定部门及其下的子部门。 如果不填，默认获取全量组织架构
     * @return mixed
     */
    static function getDeptList($accessToken, $id = null) {
        
        return self::curlGet(sprintf(self::URL_GET_DEPT_LIST, $accessToken, $id));
        
    }
    
    /**
     * 异步任务接口 - 增量更新成员
     *
     * @param string $accessToken 接口调用凭证
     * @param integer $mediaId 上传的csv文件的media_id
     * @param string $url 企业应用接收企业微信推送请求的访问协议和地址，支持http或https协议
     * @param string $token 用于生成签名
     * @param string $encodingAesKey 用于消息体的加密，是AES密钥的Base64编码
     * @return mixed json格式
     */
    static function incrementalUpdateUser(
        $accessToken, $mediaId, $url = null,
        $token = null, $encodingAesKey = null
    ) {
        
        return self::curlPost(
            sprintf(self::URL_INCREMENTAL_UPDATE_USER, $accessToken),
            json_encode([
                'media_id' => $mediaId,
                'callback' => [
                    'url'            => $url,
                    'token'          => $token,
                    'encodingaeskey' => $encodingAesKey,
                ],
            ])
        );
        
    }
    
    /**
     * 异步任务接口 - 全量覆盖成员
     *
     * @param string $accessToken 接口调用凭证
     * @param integer $mediaId 上传的csv文件的media_id
     * @param string $url 企业应用接收企业微信推送请求的访问协议和地址，支持http或https协议
     * @param string $token 用于生成签名
     * @param string $encodingAesKey 用于消息体的加密，是AES密钥的Base64编码
     * @return mixed json格式
     */
    static function overrideUser(
        $accessToken, $mediaId, $url = null,
        $token = null, $encodingAesKey = null
    ) {
        
        return self::curlPost(
            sprintf(self::URL_OVERRIDE_UPDATE_USER, $accessToken),
            json_encode([
                'media_id' => $mediaId,
                'callback' => [
                    'url'            => $url,
                    'token'          => $token,
                    'encodingaeskey' => $encodingAesKey,
                ],
            ])
        );
    }
    
    /**
     * 异步任务接口 - 全量覆盖部门
     *
     * @param string $accessToken 接口调用凭证
     * @param integer $mediaId 上传的csv文件的media_id
     * @param string $url 企业应用接收企业微信推送请求的访问协议和地址，支持http或https协议
     * @param string $token 用于生成签名
     * @param string $encodingAesKey 用于消息体的加密，是AES密钥的Base64编码
     * @return mixed json格式
     */
    static function overrideDept(
        $accessToken, $mediaId, $url = null,
        $token = null, $encodingAesKey = null
    ) {
        
        return self::curlPost(
            sprintf(self::URL_OVERRIDE_UPDATE_DEPT, $accessToken),
            json_encode([
                'media_id' => $mediaId,
                'callback' => [
                    'url'            => $url,
                    'token'          => $token,
                    'encodingaeskey' => $encodingAesKey,
                ],
            ])
        );
        
    }
    
    /**
     * 异步任务接口 - 获取异步任务结果
     *
     * @param string $accessToken 接口调用凭证
     * @param integer $jobId 异步任务id，最大长度为64字节
     * @return mixed json格式
     */
    static function getAsyncResult($accessToken, $jobId) {
        
        return self::curlGet(
            sprintf(self::URL_GET_ASYNC_RESULT, $accessToken, $jobId)
        );
        
    }
    
    /**
     * 标签管理 - 创建标签
     *
     * @param string $accessToken 接口调用凭证
     * @param string $tagName 标签名称，长度限制为32个字（汉字或英文字母），标签名不可与其他标签重名。
     * @param integer $tagId 标签id，非负整型，指定此参数时新增的标签会生成对应的标签id，不指定时则以目前最大的id自增。
     * @return mixed
     */
    static function createTag($accessToken, $tagName, $tagId = null) {
        
        return self::curlPost(
            sprintf(self::URL_CREATE_TAG, $accessToken),
            json_encode(['tagname' => $tagName, 'tagid' => $tagId])
        );
        
    }
    
    /**
     * 标签管理 - 更新标签名字
     *
     * @param string $accessToken 接口调用凭证
     * @param string $tagName 标签名称，长度限制为32个字（汉字或英文字母），标签不可与其他标签重名。
     * @param integer $tagId 标签ID
     * @return mixed json格式
     */
    static function updateTag($accessToken, $tagName, $tagId) {
        
        return self::curlPost(
            sprintf(self::URL_UPDATE_TAG, $accessToken),
            json_encode(['tagname' => $tagName, 'tagid' => $tagId])
        );
        
    }
    
    /**
     * 标签管理 - 删除标签
     *
     * @param string $accessToken 接口调用凭证
     * @param integer $tagId 标签ID
     * @return mixed json格式
     */
    static function delTag($accessToken, $tagId) {
        
        return self::curlGet(sprintf(self::URL_DEL_TAG, $accessToken, $tagId));
        
    }
    
    /**
     * 标签管理 - 获取标签成员
     *
     * @param string $accessToken 接口调用凭证
     * @param integer $tagId 标签ID
     * @return mixed json格式
     */
    static function getUserTag($accessToken, $tagId) {
        
        return self::curlGet(sprintf(self::URL_GET_TAG_USER, $accessToken, $tagId));
        
    }
    
    /**
     * 标签管理 - 增加标签成员
     *
     * @param string $accessToken 接口调用凭证
     * @param integer $tagId 标签ID
     * @param array $userList 企业成员ID列表，注意：userlist、partylist不能同时为空，单次请求长度不超过1000
     * @param array $partyList 企业部门ID列表，注意：userlist、partylist不能同时为空，单次请求长度不超过100
     * @return mixed json格式
     */
    static function addUserTag($accessToken, $tagId, $userList = null, $partyList = null) {
        
        return self::curlPost(
            sprintf(self::URL_ADD_TAG_USER, $accessToken),
            json_encode([
                'tagid'     => $tagId,
                'userlist'  => $userList,
                'partylist' => $partyList,
            ])
        );
        
    }
    
    /**
     * 标签管理 - 删除标签
     *
     * @param string $accessToken 接口调用凭证
     * @param integer $tagId 标签ID
     * @param array $userList 企业成员ID列表，注意：userlist、partylist不能同时为空，单次请求长度不超过1000
     * @param array $partyList 企业部门ID列表，注意：userlist、partylist不能同时为空，单次请求长度不超过100
     * @return mixed json格式
     */
    static function delUserTag($accessToken, $tagId, $userList = null, $partyList = null) {
        
        return self::curlPost(
            sprintf(self::URL_DEL_TAG_USER, $accessToken),
            json_encode([
                'tagId'     => $tagId,
                'userlist'  => $userList,
                'partylist' => $partyList,
            ])
        );
        
    }
    
    /**
     * 标签管理 - 获取标签列表
     *
     * @param string $accessToken 接口调用凭证
     * @return mixed json格式
     */
    static function getTagList($accessToken) {
        
        return self::curlGet(sprintf(self::URL_GET_TAG_LIST, $accessToken));
        
    }
    
    /**
     * 推送消息(文本、图片、语音、视频、文件、图文等)
     *
     * @param string $accessToken 接口调用凭证
     * @param array $message 文本、 图片、 语音、 视频、 文件、 文本卡片、 图文、 图文消息（mpnews）
     * @return mixed json格式
     */
    static function sendMessage($accessToken, $message) {
        
        return self::curlPost(
            sprintf(self::URL_SEND_MESSAGE, $accessToken),
            json_encode($message)
        );
        
    }
    
    /**
     * 应用管理 - 获取应用
     *
     * @param string $accessToken 接口调用凭证
     * @param integer $agentId 企业应用id
     * @return mixed json格式 应用的基本信息
     */
    static function getApp($accessToken, $agentId) {
        
        return self::curlPost(
            sprintf(self::URL_GET_APP, $accessToken, $agentId),
            json_encode(['access_token' => $accessToken, 'agentid' => $agentId])
        );
        
    }
    
    /**
     * 应用管理 - 设置应用
     *
     * @param string $accessToken 接口调用凭证 调用接口凭证
     * @param array $data
     * @return mixed json格式 {"errcode":0, "errmsg":"ok"}
     * @internal param int $agentId 企业应用id
     * @internal param bool $reportLocationFlag 企业应用是否打开地理位置上报 0：不上报；1：进入会话上报
     * @internal param int $logoMediaId 企业应用头像的mediaid，通过多媒体接口上传图片获得mediaid，上传后会自动裁剪成方形和圆形两个头像
     * @internal param string $name 企业应用名称
     * @internal param string $description 企业应用详情
     * @internal param string $redirectDomain 企业应用可信域名
     * @internal param bool $isReportEnter 是否上报用户进入应用事件。0：不接收；1：接收。
     * @internal param string $homeUrl 应用主页url。url必须以http或者https开头。
     */
    static function configApp($accessToken, array $data) {
        return self::curlPost(
            sprintf(self::URL_CONFIG_APP, $accessToken),
            json_encode($data)
        );
        
    }
    
    static function getAppList($accessToken) {
        //
        return self::curlGet(sprintf(self::URL_APP_LIST, $accessToken));
    }
    
    /**
     * 自定义菜单 - 创建菜单
     *
     * @param string $accessToken 接口调用凭证
     * @param integer $agentId 企业应用ID
     * @param array $menu 应用菜单数组
     * @return mixed json格式 {"errcode":0, "errmsg":"ok"}
     */
    static function createMenu($accessToken, $agentId, $menu) {
        
        return self::curlPost(
            sprintf(self::URL_CREATE_MENU, $accessToken, $agentId),
            json_encode($menu)
        );
        
    }
    
    /**
     * 自定义菜单 - 获取菜单
     *
     * @param string $accessToken 接口调用凭证
     * @param integer $agentId
     * @return mixed json格式 {"errcode":0, "errmsg":"ok"}
     */
    static function getMenu($accessToken, $agentId) {
        
        return self::curlGet(sprintf(self::URL_GET_MENU, $accessToken, $agentId));
        
    }
    
    /**
     * 账号激活
     *
     * @param string $corpId 账号名称
     * @param string $pwd 账号密码(MD5)
     * @param string $corpName 公司名称
     * @param string $contact 联系人
     * @param string $tel 座机号码
     * @param string $mobile 手机号码
     * @param string $email 电子邮件
     * @param string $remark 备注
     * @return mixed 应用的基本信息
     */
    static function activateAcct(
        $corpId, $pwd, $corpName, $contact, $mobile,
        $email = null, $remark = null, $tel = null
    ) {
        
        return self::curlGet(sprintf(
            self::URL_ACTIVATE_ACCOUNT,
            $corpId, $pwd, $corpName, $contact, $tel, $mobile, $email, $remark
        ));
        
    }
    
    /**
     * 更改密码
     *
     * @param string $corpId 账号名称
     * @param string $pwd 账号密码
     * @param string $newPwd 新密码
     * @return mixed 整数，0：密码修改成功；-1、帐号未注册；-2、其他错误；-3、密码错误
     */
    static function updatePwd($corpId, $pwd, $newPwd) {
        
        return self::curlGet(sprintf(self::URL_UPDATE_PASSWORD, $corpId, $pwd, $newPwd));
        
    }
    
    /**
     * 查询余额
     *
     * @param string $corpId 账号名称
     * @param string $pwd 账号密码
     * @return mixed 整数，>=0，剩余条数；-1、帐号未注册；-2、其他错误；-3、 密码错误；-101、
     * 调用频率过快；-100、IP黑名单；-102、账号黑名单；-103 、IP未导白
     */
    static function getBalance($corpId, $pwd) {
        
        return self::curlGet(sprintf(self::URL_GET_BALANCE, $corpId, $pwd));
        
    }
    
    /**
     * 批量发送短信
     *
     * @param string $corpId 账号名称
     * @param string $pwd 账号密码
     * @param string $mobiles 手机号码列表（用英文逗号分隔）
     * @param string $content 短消息内容
     * @param string $ext 扩展号
     * @param string $sendTime 定时发送时间（可选）
     * @return mixed 大于0的数字，发送成功；-1、帐号未注册；-2、网络访问超时，
     * 请重试；-3、密码错误；-5、余额不足；-6、定时发送时间不是有效的时间格式；
     * -7、提交信息末尾未加签名，请添加中文企业签名【 】；
     * -8、发送内容需在1到300个字之间；-9、发送号码为空；-10、
     * 定时时间不能小于系统当前时间；-100、IP黑名单；-102、账号黑名单；
     * -103、IP未导白
     * @internal param string $SendTime 定时时间,可选填
     */
    static function batchSend($corpId, $pwd, $mobiles, $content, $ext = null, $sendTime = null) {
        
        return self::curlPost(sprintf(
            self::URL_BATCH_SEND_SMS,
            $corpId, $pwd, $mobiles, $content, $ext, $sendTime
        ));
        
    }
    
    /**
     * 获取回复短信
     * 大于等于30s调用一次，过快返回-101
     *
     * @param string $corpId 账号名称
     * @param string $pwd 账号密码
     * @return mixed 字串，具体字串；-1、帐号未注册；-2、其他错误；-3、
     * 密码错误；-101、调用频率过快返回格式：||手机号#上行内容#发送时间#扩展号||手机号#上行内容#发送时间#扩展号……
     * 每次最多取50条，超过50条下次取，不足50条一次就返回完，同一条信息只能取一次，取走后系统自动更改短信标志为【已取】
     */
    static function getResponse($corpId, $pwd) {
        
        return self::curlGet(sprintf(self::URL_GET_RESPONSE_SMS, $corpId, $pwd));
    }
    
    protected static function getFacadeAccessor() { return 'Wechat'; }
    
    /**
     * 自定义菜单 - 删除菜单
     *
     * @param $accessToken
     * @param $agentId
     * @return mixed json格式 {"errcode":0, "errmsg":"ok"}
     */
    function delMenu($accessToken, $agentId) {
        
        return self::curlGet(sprintf(self::URL_DEL_MENU, $accessToken, $agentId));
        
    }
    
}