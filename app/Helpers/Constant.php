<?php
namespace App\Helpers;
/**
 * Class Constant
 * @package App\Helpers
 */
class Constant {
    
    const SYSTEM_PARAMS = [
        [
            'id'   => 'ActionType',
            'name' => '请求类型',
            'data' => [
                ['GET', null, null, ''],
                ['POST', null, null, ''],
                ['PUT', null, null, ''],
                ['DELETE', null, null, ''],
                ['HEAD', null, null, ''],
                ['OPTIONS', null, null, ''],
                ['TRACE', null, null, ''],
                ['CONNECT', null, null, ''],
                ['PATCH', null, null, ''],
            ],
        ],
        [
            'id'   => 'DepartmentType',
            'name' => '部门类型',
            'data' => [
                ['根', 'text-gray', 'fa fa-sitemap', 'root'],
                ['运营', 'text-blue', 'fa fa-building', 'company'],
                ['企业', 'text-green', 'fa fa-weixin', 'corp'],
                ['学校', 'text-purple', 'fa fa-university', 'school'],
                ['年级', 'text-black', 'fa fa-object-group', 'grade'],
                ['班级', 'text-black', 'fa fa-users', 'squad'],
                ['其他', 'text-black', 'fa fa-folder', 'other'],
            ],
        ],
        [
            'id'   => 'Group',
            'name' => '角色',
            'data' => [
                ['运营', null, null, 'company'],
                ['企业', null, null, 'corp'],
                ['学校', null, null, 'school'],
                ['监护人', null, null, 'custodian'],
                ['学生', 'text-black', 'fa fa-child', 'student'],
            ],
        ],
        [
            'id'   => 'MediaType',
            'name' => '媒体类型',
            'data' => [
                ['text', null, 'fa fa-file-text-o', '文本'],
                ['image', null, 'fa fa-file-image-o', '图片'],
                ['video', null, 'fa fa-file-movie-o', '视频'],
                ['file', null, 'fa fa-file-o', '文件'],
                ['voice', null, 'fa fa-file-sound-o', '语音'],
                ['textcard', null, 'fa fa-folder-o', '卡片'],
                ['mpnews', null, 'fa fa-th-list', '图文'],
                ['sms', null, 'fa fa-file-text', '短信'],
            ],
        ],
        [
            'id'   => 'MenuType',
            'name' => '菜单类型',
            'data' => [
                ['根', null, 'fa fa-sitemap', 'root'],
                ['运营', null, 'fa fa-building', 'company'],
                ['企业', null, 'fa fa-weixin', 'corp'],
                ['学校', null, 'fa fa-university', 'school'],
                ['其他', null, 'fa fa-foler', 'other'],
            ],
        ],
        [
            'id'   => 'MessageType',
            'name' => '消息类型',
            'data' => [
                ['成绩消息', null, null, ''],
                ['考勤消息', null, null, ''],
                ['作业消息', null, null, ''],
            ],
        ],
        [
            'id'   => 'SchoolType',
            'name' => '学校类型',
            'data' => [
                ['小学', null, null, ''],
                ['初中', null, null, ''],
                ['高中', null, null, ''],
            ],
        ],
    ];
    # 状态
    const DISABLED = 0;
    const ENABLED = 1;
    # 广播消息属性列表
    const BROADCAST_FIELDS = ['userId', 'title', 'statusCode', 'message'];
    const SUPER_ROLES = ['运营', '企业', '学校'];
    const NON_EDUCATOR = ['运营', '企业', '监护人', '学生', 'api'];
    const WEEK_DAYS = ['星期日', '星期一', '星期二', '星期三', '星期四', '星期五', '星期六'];
    const SYNC_ACTIONS = ['create' => '创建', 'update' => '更新', 'delete' => '删除'];
    # 控制器相对路径
    const CONTROLLER_DIR = 'app/Http/Controllers';
    # 无需扫描的控制器
    const EXCLUDED_CONTROLLERS = [
        'Controller',
        'HomeController',
        'SyncController',
        'ApiController',
        'TestController',
        'WechatController',
    ];
    
    /** http statuses */
    # Informational 1xx
    const CONTINUE = 100;
    const SWITCHING_PROTOCOLS = 101;
    # Success 2xx
    const OK = 200;
    const CREATED = 201;
    const ACCEPTED = 202;
    const NONAUTHORITATIVE_INFORMATION = 203;
    const NO_CONTENT = 204;
    const RESET_CONTENT = 205;
    const PARTIAL_CONTENT = 206;
    # Redirection 3xx
    const MULTIPLE_CHOICES = 300;
    const MOVED_PERMANENTLY = 301;
    const MOVED_TEMPORARILY = 302;
    const SEE_OTHER = 303;
    const NOT_MODIFIED = 304;
    const USE_PROXY = 305;
    # Client Error 4xx
    const BAD_REQUEST = 400;
    const UNAUTHORIZED = 401;
    const PAYMENT_REQUIRED = 402;
    const FORBIDDEN = 403;
    const NOT_FOUND = 404;
    const METHOD_NOT_ALLOWED = 405;
    const NOT_ACCEPTABLE = 406;
    const PROXY_AUTHENTICATION_REQUIRED = 407;
    const REQUEST_TIMEOUT = 408;
    const CONFLICT = 409;
    const GONE = 410;
    const LENGTH_REQUIRED = 411;
    const PRECONDITION_FAILED = 412;
    const REQUEST_ENTITY_TOO_LARGE = 413;
    const REQUESTURI_TOO_LARGE = 414;
    const UNSUPPORTED_MEDIA_TYPE = 415;
    const REQUESTED_RANGE_NOT_SATISFIABLE = 416;
    const EXPECTATION_FAILED = 417;
    const IM_A_TEAPOT = 418;
    const TOKEN_MISMATCH = 498;
    # Server Error 5xx
    const INTERNAL_SERVER_ERROR = 500;
    const NOT_IMPLEMENTED = 501;
    const BAD_GATEWAY = 502;
    const SERVICE_UNAVAILABLE = 503;
    const GATEWAY_TIMEOUT = 504;
    const HTTP_VERSION_NOT_SUPPORTED = 505;
    
    # 微信错误代码 & 消息
    const WXERR = [
        -1      => '系统繁忙',
        0       => '请求成功',
        10003   => 'redirect_uri域名与后台配置不一致',
        10004   => '此公众号被封禁',
        10005   => '此公众号并没有这些scope的权限',
        10006   => '必须关注此测试号',
        10009   => '操作太频繁了，请稍后重试',
        10010   => 'scope不能为空',
        10011   => 'redirect_uri不能为空',
        10012   => 'appid不能为空',
        10013   => 'state不能为空',
        10015   => '公众号未授权第三方平台，请检查授权状态',
        10016   => '不支持微信开放平台的Appid，请使用公众号Appid',
        40001   => 'secret参数错误',
        40002   => '非法凭证类型',
        40003   => 'UserID/OpenID无效',
        40004   => '非法的媒体文件类型',
        40005   => '非法的文件类型',
        40006   => '非法的文件大小',
        40007   => '非法的媒体文件media_id',
        40008   => '非法的消息类型',
        40009   => '非法的图片文件大小',
        40010   => '非法的语音文件大小',
        40011   => '非法的视频文件大小',
        40012   => '非法的缩略图文件大小',
        40013   => '非法的CorpID/AppID',
        40014   => '非法的access_token',
        40015   => '非法的菜单类型',
        40016   => '非法的按钮个数',
        40017   => '非法的按钮类型',
        40018   => '非法的按钮名字长度',
        40019   => '非法的按钮KEY长度',
        40020   => '非法的按钮URL长度',
        40021   => '非法的菜单版本号',
        40022   => '非法的子菜单级数',
        40023   => '非法的子菜单按钮个数',
        40024   => '非法的子菜单按钮类型',
        40025   => '非法的子菜单按钮名字长度',
        40026   => '非法的子菜单按钮KEY长度',
        40027   => '非法的子菜单按钮URL长度',
        40028   => '非法的自定义菜单使用用户',
        40029   => '非法的oauth_code',
        40030   => '非法的refresh_token',
        40031   => '非法的UserID/OpenID列表',
        40032   => '非法的UserID/OpenID列表长度',
        40033   => '非法的请求字符, 不得包含\uxxxx格式的字符',
        40035   => '非法的参数',
        40038   => '非法的请求格式',
        40039   => '非法的URL长度',
        40048   => '无效的url',
        40050   => 'chatid不存在/非法的分组id',
        40051   => '非法的分组名字',
        40054   => '非法的子菜单url域名',
        40055   => '非法的菜单url域名',
        40056   => '非法的agentid',
        40057   => '非法的callbackurl或者callbackurl验证失败',
        40058   => '非法的参数',
        40059   => '非法的上报地理位置标志位',
        40060   => '非法的article_idx(删除单片图文时)',
        40063   => '参数为空',
        40066   => '非法的部门列表',
        40068   => '非法的标签ID',
        40070   => '指定的标签范围结点全部无效',
        40071   => '非法的标签名字',
        40072   => '非法的标签名字长度',
        40073   => '非法的openid',
        40074   => 'news消息不支持保密消息类型',
        40077   => '非法的pre_auth_code参数',
        40078   => '非法的auth_code参数',
        40080   => '非法的suite_secret',
        40082   => '非法的suite_token',
        40083   => '非法的suite_id',
        40084   => '非法的permanent_code参数',
        40085   => '非法的的suite_ticket参数',
        40086   => '非法的第三方应用appid',
        40088   => 'jobid不存在',
        40089   => '批量任务的结果已清理',
        40091   => 'secret非法',
        40092   => '导入文件存在非法的内容',
        40093   => '非法的jsapi_ticket参数',
        40094   => '非法的URL',
        40117   => '非法的分组名字',
        40118   => '非法的media_id大小',
        40119   => 'button类型错误',
        40120   => 'button类型错误',
        40121   => '非法的media_id类型',
        40125   => '无效的appsecret',
        40132   => '非法的微信号',
        40137   => '不支持的图片格式',
        40155   => '请勿添加其他公众号的主页链接',
        40163   => 'oauth_code已使用',
        41001   => '缺少access_token参数',
        41002   => '缺少corpid/appid参数',
        41003   => '缺少refresh_token参数',
        41004   => '缺少secret参数',
        41005   => '缺少多媒体文件数据',
        41006   => '缺少media_id参数',
        41007   => '缺少子菜单数据',
        41008   => '缺少auth code参数',
        41009   => '缺少userid/openid参数',
        41010   => '缺少url参数',
        41011   => '缺少agentid参数',
        41033   => '缺少 description 参数',
        41016   => '缺少title参数',
        41019   => '缺少 department 参数',
        41017   => '缺少tagid参数',
        41021   => '缺少suite_id参数',
        41022   => '缺少suite_access_token参数',
        41023   => '缺少suite_ticket参数',
        41024   => '缺少secret参数',
        41025   => '缺少permanent_code参数',
        42001   => 'access_token已过期',
        42002   => 'refresh_token已过期',
        42003   => 'oauth_code已过期',
        42007   => 'pre_auth_code已过期/accesstoken与refreshtoken失效，需重新授权(用户修改微信密码)',
        42009   => 'suite_access_token已过期',
        43001   => '需要GET请求',
        43002   => '需要POST请求',
        43003   => '需要HTTPS请求',
        43004   => '指定的userid未绑定微信或未关注微信插件/需要接收者关注',
        43005   => '需要好友关系',
        43019   => '需要将接收者从黑名单中移除',
        44001   => '多媒体文件为空',
        44002   => 'POST的数据包为空',
        44003   => '图文消息内容为空',
        44004   => '文本消息内容(content)为空',
        45001   => '多媒体文件大小超过限制',
        45002   => '消息内容大小超过限制',
        45003   => '标题字段超过限制',
        45004   => '描述字段(description)超过限制',
        45005   => '链接字段(url)超过限制',
        45006   => '图片链接字段超过限制',
        45007   => '语音播放时间超过限制',
        45008   => '图文消息(的文章数量)超过限制',
        45009   => '接口调用超过限制',
        45010   => '创建菜单个数超过限制',
        45011   => 'API调用太频繁，请稍候再试',
        45015   => '回复时间超过限制',
        45016   => '系统分组，不允许修改',
        45017   => '分组名字过长',
        45018   => '分组数量超过上限',
        45022   => '应用name参数长度不符合系统限制',
        45024   => '帐号数量超过上限',
        45026   => '触发删除用户数的保护',
        45032   => '图文消息author参数长度超过限制',
        45033   => '接口并发调用超过限制',
        45047   => '客服接口下行条数超过上限',
        46001   => '不存在媒体数据',
        46002   => '不存在的菜单版本',
        46003   => '菜单未设置/不存在的菜单数据',
        46004   => '指定用户不存在/不存在的用户',
        47001   => '解析JSON/XML内容失败',
        48001   => 'api功能未授权，请确认公众号已开通该接口权限',
        48002   => 'API接口无权限调用/粉丝拒收消息(公众号“接收消息”选项已被关闭)',
        48003   => '非法的suite_id',
        48004   => '授权关系无效/api接口被封禁',
        48005   => 'API接口已废弃/api禁止删除被自动回复和自定义菜单引用的素材',
        48006   => 'api禁止清零调用次数，因为清零次数达到上限',
        48008   => '没有该类型消息的发送权限',
        50001   => 'redirect_url未登记可信域名/用户未授权该api',
        50002   => '成员不在权限范围/用户受限，可能是违规后接口被封禁',
        50003   => '应用已禁用',
        50005   => '用户未关注公众号',
        60001   => '部门长度不符合限制',
        60003   => '部门ID不存在',
        60004   => '父部门不存在',
        60005   => '部门下存在成员',
        60006   => '部门下存在子部门',
        60007   => '不允许删除根部门',
        60008   => '部门已存在',
        60009   => '部门名称含有非法字符',
        60010   => '部门存在循环关系',
        60011   => '指定的成员/部门/标签参数无权限',
        60012   => '不允许删除默认应用',
        60020   => '访问ip不在白名单之中',
        60028   => '不允许修改第三方应用的主页 URL',
        60102   => 'UserID已存在',
        60103   => '手机号码非法',
        60104   => '手机号码已存在',
        60105   => '邮箱非法',
        60106   => '邮箱已存在',
        60107   => '微信号非法',
        60110   => '用户所属部门数量超过限制',
        60111   => 'UserID不存在',
        60112   => '成员name参数非法',
        60123   => '无效的部门id',
        60124   => '无效的父部门id',
        60125   => '非法部门名字',
        60127   => '缺少department参数',
        60129   => '成员手机和邮箱都为空',
        61451   => '参数错误',
        61452   => '无效客服账号',
        61453   => '客服帐号已存在',
        61454   => '客服帐号名长度超过限制',
        61455   => '客服帐号名包含非法字符',
        61456   => '客服帐号个数超过限制',
        61457   => '无效头像文件类型',
        61450   => '系统错误',
        61500   => '日期格式错误',
        63001   => '部分参数为空',
        63002   => '无效的签名',
        65301   => '不存在此menuid对应的个性化菜单',
        65302   => '没有相应的用户',
        65303   => '没有默认菜单，不能创建个性化菜单',
        65304   => 'MatchRule信息为空',
        65305   => '个性化菜单数量受限',
        65306   => '不支持个性化菜单的帐号',
        65307   => '个性化菜单信息为空',
        65308   => '包含没有响应类型的button',
        65309   => '个性化菜单开关处于关闭状态',
        65310   => '填写了省份或城市信息，国家信息不能为空',
        65311   => '填写了城市信息，省份信息不能为空',
        65312   => '非法的国家信息',
        65313   => '非法的省份信息',
        65314   => '非法的城市信息',
        65316   => '该公众号的菜单设置了过多的域名外跳转',
        65317   => '非法的URL',
        72023   => '发票已被其他公众号锁定',
        72024   => '发票状态错误',
        72037   => '存在发票不属于该用户',
        80001   => '可信域名不正确，或者无ICP备案',
        81001   => '部门下的结点数超过限制（3W）',
        81002   => '部门最多15层',
        81011   => '无权限操作标签',
        81013   => 'UserID、部门ID、标签ID全部非法或无权限',
        81014   => '标签添加成员，单次添加user或party过多',
        82001   => '指定的成员/部门/标签全部无效',
        82002   => '非法的PartyID列表长度',
        82003   => '非法的TagID列表长度',
        84014   => '成员票据过期',
        84015   => '成员票据无效',
        84019   => '缺少templateid参数',
        84020   => 'templateid不存在',
        84021   => '缺少register_code参数',
        84022   => '无效的register_code参数',
        84023   => '不允许调用设置通讯录同步完成接口',
        84024   => '无注册信息',
        84025   => '不符合的state参数',
        85002   => '包含非法的词语',
        85004   => '每企业每个月设置的可信域名不可超过20个',
        85005   => '可信域名未通过所有权校验',
        86001   => '参数 chatid 非法',
        86003   => '参数 chatid 不存在',
        86004   => '参数 群名非法',
        86005   => '参数 群主非法',
        86006   => '群成员数过多或过少',
        86007   => '非法的群成员',
        86008   => '非法操作非自己创建的群',
        86216   => '存在非法会话成员ID',
        86217   => '会话发送者不在会话成员列表中',
        86220   => '指定的会话参数非法',
        87009   => '无效的签名',
        90001   => '未认证摇一摇周边',
        90002   => '缺少摇一摇周边ticket参数',
        90003   => '摇一摇周边ticket参数非法',
        90100   => '非法的对外属性类型',
        90101   => '对外属性：文本类型长度非法',
        90102   => '对外属性：网页类型标题长度非法',
        90103   => '对外属性：网页url非法',
        90104   => '对外属性：小程序类型标题长度非法',
        90105   => '对外属性：小程序类型pagepath非法',
        90106   => '对外属性：请求参数非法',
        91040   => '获取ticket的类型无效',
        301002  => '无权限操作指定的应用',
        301005  => '不允许删除创建者',
        301012  => '参数position非法',
        301013  => '参数telephone非法',
        301014  => '参数english_name非法',
        301015  => '参数mediaid非法',
        301016  => '上传语音文件不符合系统要求',
        301017  => '上传语音文件仅支持AMR格式',
        301021  => '参数 userid 无效',
        301022  => '获取打卡数据失败',
        301023  => 'useridlist非法或超过限额',
        301024  => '获取打卡记录时间间隔超限',
        301036  => '不允许更新该用户的userid',
        302003  => '批量导入任务的文件中userid有重复',
        302004  => '组织架构非法',
        302005  => '批量导入系统失败，请重新尝试导入',
        302006  => '批量导入任务的文件中partyid有重复',
        302007  => '批量导入任务的文件中，同一个部门下有两个子部门名字一样',
        2000002 => 'CorpId参数无效',
        9001001 => '非法的POST数据参数',
        9001002 => '远端服务不可用',
        9001003 => '非法的Ticket',
        9001004 => '获取摇周边用户信息失败',
        9001005 => '获取商户信息失败',
        9001006 => '获取 OpenID 失败',
        9001007 => '上传文件缺失',
        9001008 => '上传素材的文件类型非法',
        9001009 => '上传素材的文件尺寸非法',
        9001010 => '上传失败',
        9001020 => '帐号非法',
        9001021 => '已有设备激活率低于50%，不能新增设备',
        9001022 => '设备申请数非法，必须为大于0的数字',
        9001023 => '已存在审核中的设备ID申请',
        9001024 => '一次查询设备ID数量不能超过50',
        9001025 => '设备ID非法',
        9001026 => '页面ID非法',
        9001027 => '页面参数非法',
        9001028 => '一次删除页面ID数量不能超过10',
        9001029 => '页面已应用在设备中，请先解除应用关系再删除',
        9001030 => '一次查询页面ID数量不能超过50',
        9001031 => '时间区间非法',
        9001032 => '保存设备与页面的绑定关系参数错误',
        9001033 => '门店ID非法',
        9001034 => '设备备注信息过长',
        9001035 => '设备申请参数非法',
        9001036 => '查询起始值(begin)非法',
    ];
    # 微信公众号所属行业
    const INDUSTRY = [
        1  => 'IT科技:互联网/电子商务',
        2  => 'IT科技:IT软件与服务',
        3  => 'IT科技:IT硬件与设备',
        4  => 'IT科技:电子技术',
        5  => 'IT科技:通信与运营商',
        6  => 'IT科技:网络游戏',
        7  => '金融业:银行',
        8  => '金融业:基金理财信托',
        9  => '金融业:保险',
        10 => '餐饮:餐饮',
        11 => '酒店旅游:酒店',
        12 => '酒店旅游:旅游',
        13 => '运输与仓储:快递',
        14 => '运输与仓储:物流',
        15 => '运输与仓储:仓储',
        16 => '教育:培训',
        17 => '教育:院校',
        18 => '政府与公共事业:学术科研',
        19 => '政府与公共事业:交警',
        20 => '政府与公共事业:博物馆',
        21 => '政府与公共事业:公共事业非盈利机构',
        22 => '医药护理:医药医疗',
        23 => '医药护理:护理美容',
        24 => '医药护理:保健与卫生',
        25 => '交通工具:汽车相关',
        26 => '交通工具:摩托车相关',
        27 => '交通工具:火车相关',
        28 => '交通工具:飞机相关',
        29 => '房地产:建筑',
        30 => '房地产:物业',
        31 => '消费品:消费品',
        32 => '商业服务:法律',
        33 => '商业服务:会展',
        34 => '商业服务:中介服务',
        35 => '商业服务:认证',
        36 => '商业服务:审计',
        37 => '文体娱乐:传媒',
        38 => '文体娱乐:体育',
        39 => '文体娱乐:娱乐休闲',
        40 => '印刷:印刷',
        41 => '其它:其它',
    ];
    
    # field names
    const USER_FIELDS = [
        'username', 'group_id', 'password', 'realname', 'gender', 'mobile', 'enabled', 'ent_attrs',
    ];
    const MEMBER_FIELDS = [
        'userid', 'username', 'position', 'name', 'english_name',
        'mobile', 'email', 'department', 'gender', 'remark', 'enable',
    ];
}