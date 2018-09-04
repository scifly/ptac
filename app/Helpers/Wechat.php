<?php
namespace App\Helpers;

use App\Models\App;
use App\Models\Corp;
use Exception;

/**
 * Class Wechat
 * @package App\Helpers
 */
class Wechat {
    
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
    
    # 错误代码 & 消息
    const ERRMSGS = [
        -1 => '系统繁忙',
        0 => '请求成功',
        40001 => '不合法的secret参数',
        40003 => '无效的UserID',
        40004 => '不合法的媒体文件类型',
        40005 => '不合法的type参数',
        40006 => '不合法的文件大小',
        40007 => '不合法的media_id参数',
        40008 => '不合法的msgtype参数',
        40009 => '上传图片大小不是有效值',
        40011 => '上传视频大小不是有效值',
        40013 => '不合法的CorpID',
        40014 => '不合法的access_token',
        40016 => '不合法的按钮个数',
        40017 => '不合法的按钮类型',
        40018 => '不合法的按钮名字长度',
        40019 => '不合法的按钮KEY长度',
        40020 => '不合法的按钮URL长度',
        40022 => '不合法的子菜单级数',
        40023 => '不合法的子菜单按钮个数',
        40024 => '不合法的子菜单按钮类型',
        40025 => '不合法的子菜单按钮名字长度',
        40026 => '不合法的子菜单按钮KEY长度',
        40027 => '不合法的子菜单按钮URL长度',
        40029 => '不合法的oauth_code',
        40031 => '不合法的UserID列表',
        40032 => '不合法的UserID列表长度',
        40033 => '不合法的请求字符',
        40035 => '不合法的参数',
        40050 => 'chatid不存在',
        40054 => '不合法的子菜单url域名',
        40055 => '不合法的菜单url域名',
        40056 => '不合法的agentid',
        40057 => '不合法的callbackurl或者callbackurl验证失败',
        40058 => '不合法的参数',
        40059 => '不合法的上报地理位置标志位',
        40063 => '参数为空',
        40066 => '不合法的部门列表',
        40068 => '不合法的标签ID',
        40070 => '指定的标签范围结点全部无效',
        40071 => '不合法的标签名字',
        40072 => '不合法的标签名字长度',
        40073 => '不合法的openid',
        40074 => 'news消息不支持保密消息类型',
        40077 => '不合法的pre_auth_code参数',
        40078 => '不合法的auth_code参数',
        40080 => '不合法的suite_secret',
        40082 => '不合法的suite_token',
        40083 => '不合法的suite_id',
        40084 => '不合法的permanent_code参数',
        40085 => '不合法的的suite_ticket参数',
        40086 => '不合法的第三方应用appid',
        40088 => 'jobid不存在',
        40089 => '批量任务的结果已清理',
        40091 => 'secret不合法',
        40092 => '导入文件存在不合法的内容',
        40093 => '不合法的jsapi_ticket参数',
        40094 => '不合法的URL',
        41001 => '缺少access_token参数',
        41002 => '缺少corpid参数',
        41004 => '缺少secret参数',
        41006 => '缺少media_id参数',
        41008 => '缺少auth code参数',
        41009 => '缺少userid参数',
        41010 => '缺少url参数',
        41011 => '缺少agentid参数',
        41033 => '缺少 description 参数',
        41016 => '缺少title参数',
        41019 => '缺少 department 参数',
        41017 => '缺少tagid参数',
        41021 => '缺少suite_id参数',
        41022 => '缺少suite_access_token参数',
        41023 => '缺少suite_ticket参数',
        41024 => '缺少secret参数',
        41025 => '缺少permanent_code参数',
        42001 => 'access_token已过期',
        42007 => 'pre_auth_code已过期',
        42009 => 'suite_access_token已过期',
        43004 => '指定的userid未绑定微信或未关注微信插件',
        44001 => '多媒体文件为空',
        44004 => '文本消息content参数为空',
        45001 => '多媒体文件大小超过限制',
        45002 => '消息内容大小超过限制',
        45004 => '应用description参数长度不符合系统限制',
        45007 => '语音播放时间超过限制',
        45008 => '图文消息的文章数量不符合系统限制',
        45009 => '接口调用超过限制',
        45022 => '应用name参数长度不符合系统限制',
        45024 => '帐号数量超过上限',
        45026 => '触发删除用户数的保护',
        45032 => '图文消息author参数长度超过限制',
        45033 => '接口并发调用超过限制',
        46003 => '菜单未设置',
        46004 => '指定的用户不存在',
        48002 => 'API接口无权限调用',
        48003 => '不合法的suite_id',
        48004 => '授权关系无效',
        48005 => 'API接口已废弃',
        50001 => 'redirect_url未登记可信域名',
        50002 => '成员不在权限范围',
        50003 => '应用已禁用',
        60001 => '部门长度不符合限制',
        60003 => '部门ID不存在',
        60004 => '父部门不存在',
        60005 => '部门下存在成员',
        60006 => '部门下存在子部门',
        60007 => '不允许删除根部门',
        60008 => '部门已存在',
        60009 => '部门名称含有非法字符',
        60010 => '部门存在循环关系',
        60011 => '指定的成员/部门/标签参数无权限',
        60012 => '不允许删除默认应用',
        60020 => '访问ip不在白名单之中',
        60028 => '不允许修改第三方应用的主页 URL',
        60102 => 'UserID已存在',
        60103 => '手机号码不合法',
        60104 => '手机号码已存在',
        60105 => '邮箱不合法',
        60106 => '邮箱已存在',
        60107 => '微信号不合法',
        60110 => '用户所属部门数量超过限制',
        60111 => 'UserID不存在',
        60112 => '成员name参数不合法',
        60123 => '无效的部门id',
        60124 => '无效的父部门id',
        60125 => '非法部门名字',
        60127 => '缺少department参数',
        60129 => '成员手机和邮箱都为空',
        72023 => '发票已被其他公众号锁定',
        72024 => '发票状态错误',
        72037 => '存在发票不属于该用户',
        80001 => '可信域名不正确，或者无ICP备案',
        81001 => '部门下的结点数超过限制（3W）',
        81002 => '部门最多15层',
        81011 => '无权限操作标签',
        81013 => 'UserID、部门ID、标签ID全部非法或无权限',
        81014 => '标签添加成员，单次添加user或party过多',
        82001 => '指定的成员/部门/标签全部无效',
        82002 => '不合法的PartyID列表长度',
        82003 => '不合法的TagID列表长度',
        84014 => '成员票据过期',
        84015 => '成员票据无效',
        84019 => '缺少templateid参数',
        84020 => 'templateid不存在',
        84021 => '缺少register_code参数',
        84022 => '无效的register_code参数',
        84023 => '不允许调用设置通讯录同步完成接口',
        84024 => '无注册信息',
        84025 => '不符合的state参数',
        85002 => '包含不合法的词语',
        85004 => '每企业每个月设置的可信域名不可超过20个',
        85005 => '可信域名未通过所有权校验',
        86001 => '参数 chatid 不合法',
        86003 => '参数 chatid 不存在',
        86004 => '参数 群名不合法',
        86005 => '参数 群主不合法',
        86006 => '群成员数过多或过少',
        86007 => '不合法的群成员',
        86008 => '非法操作非自己创建的群',
        86216 => '存在非法会话成员ID',
        86217 => '会话发送者不在会话成员列表中',
        86220 => '指定的会话参数不合法',
        90001 => '未认证摇一摇周边',
        90002 => '缺少摇一摇周边ticket参数',
        90003 => '摇一摇周边ticket参数不合法',
        90100 => '非法的对外属性类型',
        90101 => '对外属性：文本类型长度不合法',
        90102 => '对外属性：网页类型标题长度不合法',
        90103 => '对外属性：网页url不合法',
        90104 => '对外属性：小程序类型标题长度不合法',
        90105 => '对外属性：小程序类型pagepath不合法',
        90106 => '对外属性：请求参数不合法',
        91040 => '获取ticket的类型无效',
        301002 => '无权限操作指定的应用',
        301005 => '不允许删除创建者',
        301012 => '参数 position 不合法',
        301013 => '参数 telephone 不合法',
        301014 => '参数 english_name 不合法',
        301015 => '参数 mediaid 不合法',
        301016 => '上传语音文件不符合系统要求',
        301017 => '上传语音文件仅支持AMR格式',
        301021 => '参数 userid 无效',
        301022 => '获取打卡数据失败',
        301023 => 'useridlist非法或超过限额',
        301024 => '获取打卡记录时间间隔超限',
        301036 => '不允许更新该用户的userid',
        302003 => '批量导入任务的文件中userid有重复',
        302004 => '组织架构不合法（1不是一棵树，2 多个一样的partyid，3 partyid空，4 partyid name 空，5 同一个父节点下有两个子节点 部门名字一样 可能是以上情况，请一一排查）',
        302005 => '批量导入系统失败，请重新尝试导入',
        302006 => '批量导入任务的文件中partyid有重复',
        302007 => '批量导入任务的文件中，同一个部门下有两个子部门名字一样',
        2000002 => 'CorpId参数无效'
    ];
    
    /**
     * 获取access_token
     *
     * @param string $corpid 企业号ID
     * @param string $secret 应用secret
     * @param bool $contactSync
     * @return bool|mixed
     * @throws Exception
     */
    function getAccessToken($corpid, $secret, $contactSync = false) {
        
        $app = !$contactSync
            ? App::whereSecret($secret)->first()
            : Corp::whereContactSyncSecret($secret)->first();
        if ($app) {
            if ($app['expire_at'] < time() || !isset($app['expire_at'])) {
                $token = self::_token($corpid, $secret);
                if ($token['errcode'] == 0) {
                    $app->update([
                        'expire_at' => date('Y-m-d H:i:s', time() + 7000),
                        'access_token' => $token['access_token']
                    ]);
                }
            } else {
                $token = ['errcode' => 0, 'access_token' => $app['access_token']];
            }
        } else {
            $token = self::_token($corpid, $secret);
        }

        return $token;
        
    }
    
    /**
     * 生成“获取Code”的链接地址，并返回封装后的页面跳转javascript脚本
     *
     * @param string $corpId 企业号ID
     * @param integer $agentId 企业应用ID
     * @param string $redirectUri 授权后重定向的回调链接地址
     * @return string 返回回调地址跳转javascript脚本字符串
     */
    function getCodeUrl($corpId, $agentId, $redirectUri) {
        
        $url = sprintf(
            self::URL_GET_CODE,
            $corpId,
            urlencode($redirectUri),
            self::SCOPE_USERINFO,
            $agentId
        );
        return $url;
        // return "window.location = \"{$url}\"";
        
    }
    
    /**
     * 根据access_token和code获取用户详细信息（除电话和邮箱外）
     *
     * @param string $accessToken 接口调用凭证
     * @param string $code 通过成员授权获取到的code，每次成员授权带上的code将不一样，code只能使用一次，10分钟未被使用自动过期
     * @return mixed
     * @throws Exception
     */
    function getUserInfo($accessToken, $code) {

        return $this->curlGet(sprintf(
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
     * @throws Exception
     */
    function getUserDetail($accessToken) {
        
        return self::curlPost(
            sprintf(self::URL_GET_USERDETAIL, $accessToken),
            json_encode(['user_ticket' => 'USER_TICKET'])
        );
        
    }
    
    /**
     * 根据access_token, userid和agentid将userid转换成openid
     *
     * @param string $accessToken 接口调用凭证
     * @param string $userId 微信用户ID
     * @param integer $agentId 企业应用ID
     * @return mixed
     * @throws Exception
     */
    function convertToOpenid($accessToken, $userId, $agentId) {
        
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
     * @throws Exception
     */
    function convertToUserId($accessToken, $openid) {
        
        return $this->curlPost(
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
     * @throws Exception
     */
    function getLoginInfo($accessToken, $authCode) {
        
        return $this->curlPost(
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
     * @throws Exception
     */
    function getLoginUrl($accessToken, $loginTicket, $target, $agentId = null) {
        
        return $this->curlPost(
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
     * @throws Exception
     */
    function createUser(
        $accessToken, array $data
    ) {
        return $this->curlPost(
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
     * @throws Exception
     */
    function getUser($accessToken, $userId) {
        
        return $this->curlGet(sprintf(self::URL_GET_USER, $accessToken, $userId));
        
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
     * @throws Exception
     */
    function updateUser(
        $accessToken, array $data
    ) {
        
        return $this->curlPost(
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
     * @throws Exception
     */
    function deleteUser($accessToken, $userId) {
        
        return $this->curlGet(sprintf(self::URL_DEL_USER, $accessToken, $userId));
        
    }
    
    /**
     * 成员管理 - 批量删除成员
     *
     * @param string $accessToken 接口调用凭证
     * @param array $userIdList 成员UserID列表。对应管理端的帐号
     * @return mixed json格式
     * @throws Exception
     */
    function batchDelUser($accessToken, $userIdList) {
        
        return $this->curlPost(
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
     * @throws Exception
     */
    function getDeptUser($accessToken, $departmentId, $fetchChild = null) {
        
        return $this->curlGet(sprintf(self::URL_GET_DEPT_USER, $accessToken, $departmentId, $fetchChild));
        
    }
    
    /**
     * 成员管理 - 获取部门成员详情
     *
     * @param string $accessToken 接口调用凭证
     * @param integer $departmentId 获取的部门ID
     * @param bool $fetch_child 1/0：是否递归获取子部门下面的成员
     * @return mixed json格式
     * @throws Exception
     */
    function getDeptUserDetail($accessToken, $departmentId, $fetch_child = null) {
        
        return $this->curlGet(sprintf(self::URL_GET_DEPT_USER_DETAIL, $accessToken, $departmentId, $fetch_child));
        
    }
    
    /**
     * 部门管理 - 创建部门
     *
     * @param string $accessToken 接口调用凭证
     * @param array $data
     * @return mixed
     * @throws Exception
     */
    function createDept($accessToken, array $data) {
        
        return $this->curlPost(
            sprintf(self::URL_CREATE_DEPT, $accessToken),
            json_encode($data)
        );
    }
    
    /**
     * 部门管理 - 更新部门
     *
     * @param string $accessToken 接口调用凭证
     * @param array $data
     * @return mixed {"errcode": 0, "errmsg": "updated"}
     * @throws Exception
     */
    function updateDept($accessToken, array $data) {
        
        return $this->curlPost(
            sprintf(self::URL_UPDATE_DEPT, $accessToken),
            json_encode($data)
        );
        
    }
    
    /**
     * 部门管理 - 删除部门
     *
     * @param string $accessToken 接口调用凭证
     * @param integer $id 部门id。（注：不能删除根部门；不能删除含有子部门、成员的部门）
     * @return mixed {"errcode": 0, "errmsg": "updated"}
     * @throws Exception
     */
    function deleteDept($accessToken, $id) {
        
        return $this->curlGet(sprintf(self::URL_DEL_DEPT, $accessToken, $id));
        
    }
    
    /**
     * 部门管理 - 获取部门列表
     *
     * @param string $accessToken 接口调用凭证
     * @param integer $id 部门id。获取指定部门及其下的子部门。 如果不填，默认获取全量组织架构
     * @return mixed
     * @throws Exception
     */
    function getDeptList($accessToken, $id = null) {
        
        return $this->curlGet(sprintf(self::URL_GET_DEPT_LIST, $accessToken, $id));
        
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
     * @throws Exception
     */
    function incrementalUpdateUser(
        $accessToken, $mediaId, $url = null,
        $token = null, $encodingAesKey = null
    ) {
        
        return $this->curlPost(
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
     * @throws Exception
     */
    function overrideUser(
        $accessToken, $mediaId, $url = null,
        $token = null, $encodingAesKey = null
    ) {
        
        return $this->curlPost(
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
     * @throws Exception
     */
    function overrideDept(
        $accessToken, $mediaId, $url = null,
        $token = null, $encodingAesKey = null
    ) {
        
        return $this->curlPost(
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
     * @throws Exception
     */
    function getAsyncResult($accessToken, $jobId) {
        
        return $this->curlGet(
            sprintf(self::URL_GET_ASYNC_RESULT, $accessToken, $jobId)
        );
        
    }
    
    /**
     * 标签管理 - 创建标签
     *
     * @param string $accessToken 接口调用凭证
     * @param array $data -
     *      tagname: 标签名称，长度限制为32个字（汉字或英文字母），标签不可与其他标签重名。
     *      tagid: 标签id
     * @return mixed
     * @throws Exception
     */
    function createTag($accessToken, array $data) {
        
        return $this->curlPost(
            sprintf(self::URL_CREATE_TAG, $accessToken),
            json_encode($data)
        );
        
    }
    
    /**
     * 标签管理 - 更新标签名字
     *
     * @param string $accessToken 接口调用凭证
     * @param array $data
     *      tagname: 标签名称，长度限制为32个字（汉字或英文字母），标签不可与其他标签重名。
     *      tagid: 标签id
     * @return mixed json格式
     * @throws Exception
     */
    function updateTag($accessToken, array $data) {
        
        return $this->curlPost(
            sprintf(self::URL_UPDATE_TAG, $accessToken),
            json_encode($data)
        );
        
    }
    
    /**
     * 标签管理 - 删除标签
     *
     * @param string $accessToken 接口调用凭证
     * @param integer $tagId 标签ID
     * @return mixed json格式
     * @throws Exception
     */
    function deleteTag($accessToken, $tagId) {
        
        return $this->curlGet(sprintf(self::URL_DEL_TAG, $accessToken, $tagId));
        
    }
    
    /**
     * 标签管理 - 获取标签成员
     *
     * @param string $accessToken 接口调用凭证
     * @param integer $tagId 标签ID
     * @return mixed json格式
     * @throws Exception
     */
    function getTagMember($accessToken, $tagId) {
        
        return $this->curlGet(sprintf(self::URL_GET_TAG_USER, $accessToken, $tagId));
        
    }
    
    /**
     * 标签管理 - 增加标签成员
     *
     * @param string $accessToken 接口调用凭证
     * @param array $data
     *      tagId 标签ID
     *      userList 企业成员ID列表，注意：userlist、partylist不能同时为空，单次请求长度不超过1000
     *      partyList 企业部门ID列表，注意：userlist、partylist不能同时为空，单次请求长度不超过100
     * @return mixed json格式
     * @throws Exception
     */
    function addTagMember($accessToken, array $data) {
        
        return $this->curlPost(
            sprintf(self::URL_ADD_TAG_USER, $accessToken),
            json_encode($data)
        );
        
    }
    
    /**
     * 标签管理 - 删除标签成员
     *
     * @param string $accessToken 接口调用凭证
     * @param array $data
     *      tagId 标签ID
     *      userList 企业成员ID列表，注意：userlist、partylist不能同时为空，单次请求长度不超过1000
     *      partyList 企业部门ID列表，注意：userlist、partylist不能同时为空，单次请求长度不超过100
     * @return mixed json格式
     * @throws Exception
     */
    function delTagMember($accessToken, array $data) {
        
        return $this->curlPost(
            sprintf(self::URL_DEL_TAG_USER, $accessToken),
            json_encode($data)
        );
        
    }
    
    /**
     * 标签管理 - 获取标签列表
     *
     * @param string $accessToken 接口调用凭证
     * @return mixed json格式
     * @throws Exception
     */
    function getTagList($accessToken) {
        
        return $this->curlGet(sprintf(self::URL_GET_TAG_LIST, $accessToken));
        
    }
    
    /**
     * 推送消息(文本、图片、语音、视频、文件、图文等)
     *
     * @param string $accessToken 接口调用凭证
     * @param array $message 文本、 图片、 语音、 视频、 文件、 文本卡片、 图文、 图文消息（mpnews）
     * @return mixed json格式
     * @throws Exception
     */
    function sendMessage($accessToken, $message) {
        
        return $this->curlPost(
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
     * @throws Exception
     */
    function getApp($accessToken, $agentId) {
        
        return $this->curlPost(
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
     * @throws Exception
     */
    function configApp($accessToken, array $data) {
        return $this->curlPost(
            sprintf(self::URL_CONFIG_APP, $accessToken),
            json_encode($data)
        );
        
    }
    
    /**
     * 获取应用列表
     *
     * @param $accessToken
     * @return mixed
     * @throws Exception
     */
    function getAppList($accessToken) {
        //
        return $this->curlGet(sprintf(self::URL_APP_LIST, $accessToken));
    }
    
    /**
     * 自定义菜单 - 创建菜单
     *
     * @param string $accessToken 接口调用凭证
     * @param integer $agentId 企业应用ID
     * @param array $menu 应用菜单数组
     * @return mixed json格式 {"errcode":0, "errmsg":"ok"}
     * @throws Exception
     */
    function createMenu($accessToken, $agentId, $menu) {
        
        return $this->curlPost(
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
     * @throws Exception
     */
    function getMenu($accessToken, $agentId) {
        
        return $this->curlGet(sprintf(self::URL_GET_MENU, $accessToken, $agentId));
        
    }
    
    /**
     * 自定义菜单 - 删除菜单
     *
     * @param $accessToken
     * @param $agentId
     * @return mixed json格式 {"errcode":0, "errmsg":"ok"}
     * @throws Exception
     */
    function delMenu($accessToken, $agentId) {
        
        return $this->curlGet(
            sprintf(self::URL_DEL_MENU, $accessToken, $agentId)
        );
        
    }
    
    /**
     * 上传临时素材
     *
     * @param $accessToken
     * @param $type
     * @param $data
     * @return mixed|null
     * @throws Exception
     */
    function uploadMedia($accessToken, $type, $data) {

        return $this->curlPost(
            sprintf(self::URL_UPLOAD_MEDIA, $accessToken, $type),
            $data
        );

    }
    
    /** 发送短信 ----------------------------------------------------------------------------------------------------- */
    function sendSms() {

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
     * @throws Exception
     */
    function activateAcct(
        $corpId, $pwd, $corpName, $contact, $mobile,
        $email = null, $remark = null, $tel = null
    ) {
        
        return $this->curlGet(sprintf(
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
     * @throws Exception
     */
    function updatePwd($corpId, $pwd, $newPwd) {
        
        return $this->curlGet(sprintf(self::URL_UPDATE_PASSWORD, $corpId, $pwd, $newPwd));
        
    }
    
    /**
     * 查询余额
     *
     * @param string $corpId 账号名称
     * @param string $pwd 账号密码
     * @return mixed 整数，>=0，剩余条数；-1、帐号未注册；-2、其他错误；-3、 密码错误；-101、
     * 调用频率过快；-100、IP黑名单；-102、账号黑名单；-103 、IP未导白
     * @throws Exception
     */
    function getBalance($corpId, $pwd) {
        
        return $this->curlGet(sprintf(self::URL_GET_BALANCE, $corpId, $pwd));
        
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
     * @throws Exception
     */
    function batchSend($corpId, $pwd, $mobiles, $content, $ext = null, $sendTime = null) {
        
        return $this->curlPost(sprintf(
            self::URL_BATCH_SEND_SMS,
            $corpId, $pwd, $mobiles, $content, $ext, $sendTime
        ), []);
        
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
     * @throws Exception
     */
    function getResponse($corpId, $pwd) {
        
        return $this->curlGet(
            sprintf(self::URL_GET_RESPONSE_SMS, $corpId, $pwd)
        );
        
    }
    
    /** Helper functions */
    /**
     * 发送GET请求
     *
     * @param $url
     * @return mixed
     * @throws Exception
     */
    private function curlGet($url) {
        
        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
            $result = curl_exec($ch);
            if (!$result) {
                throw new Exception(curl_error($ch), curl_errno($ch));
            }
            curl_close($ch);
        } catch (Exception $e) {
            throw $e;
        }
        
        return $result;
        
    }
    
    /**
     * 发送POST请求
     *
     * @param $url
     * @param mixed $formData
     * @return mixed|null
     * @throws Exception
     */
    private function curlPost($url, $formData) {
        
        $result = null;
        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $formData);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $result = curl_exec($ch);
            // Check the return value of curl_exec(), too
            if (!$result) {
                throw new Exception(curl_error($ch), curl_errno($ch));
            }
            curl_close($ch);
        } catch (Exception $e) {
            throw $e;
        }
        
        return $result;
        
    }
    
    /**
     * 获取accessToken
     *
     * @param $corpid
     * @param $secret
     * @return array
     * @throws Exception
     */
    private function _token($corpid, $secret) {
        
        $result = json_decode(
            $this->curlGet(sprintf(self::URL_GET_ACCESSTOKEN, $corpid, $secret))
        );
        $errcode = $result->{'errcode'};
        
        return $errcode == 0
            ? ['errcode' => 0, 'access_token' => $result->{'access_token'}]
            : ['errcode' => $errcode, 'errmsg' => Constant::WXERR[$errcode]];
        
    }
    
}