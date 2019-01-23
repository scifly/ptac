<?php
namespace App\Helpers;
/**
 * Class Constant
 * @package App\Helpers
 */
class Constant {
    
    # 状态
    const DISABLED = 0;
    const ENABLED = 1;
    # 广播消息属性列表
    const BROADCAST_FIELDS = ['userId', 'title', 'statusCode', 'message'];
    
    const SUPER_ROLES = ['运营', '企业', '学校'];
    const NON_EDUCATOR = ['运营', '企业', '监护人', '学生', 'api'];
    const BATCH_OPERATIONS = ['enable', 'disable', 'delete'];
    # 企业管理员可访问的运营类功能
    const ALLOWED_CORP_ACTIONS = [
        'corps/edit/%s',
        'corps/update/%s',
    ];
    # 校级管理员可访问的企业类功能
    const ALLOWED_SCHOOL_ACTIONS = [
        'schools/show/%s',
        'schools/edit/%s',
        'schools/update/%s',
    ];
    const ALLOWED_WAPSITE_ACTIONS = [
        'wap_sites/show/%s',
        'wap_sites/edit/%s',
        'wap_sites/update/%s',
    ];
    const WEEK_DAYS = [
        '星期日', '星期一', '星期二', '星期三',
        '星期四', '星期五', '星期六',
    ];
    const SYNC_ACTIONS = [
        'create' => '创建',
        'update' => '更新',
        'delete' => '删除',
    ];
    const ROOT_DEPARTMENT_ID = 1;
    const DEPARTMENT_TYPES = [
        '根'  => 'root',
        '运营' => 'company',
        '企业' => 'corp',
        '学校' => 'school',
        '年级' => 'grade',
        '班级' => 'class',
        '其他' => 'other',
    ];
    const MENU_TYPES = [
        '根'  => 'root',
        '运营' => 'company',
        '企业' => 'corp',
        '学校' => 'school',
        '其他' => 'other',
    ];
    const MEDIA_TYPE_ICONS = [
        'text' => '<i class="fa fa-file-text-o"></i>',
        'image' => '<i class="fa fa-file-image-o"></i>',
        'voice' => '<i class="fa fa-file-sound-o"></i>',
        'video' => '<i class="fa fa-file-movie-o"></i>',
        'file' => '<i class="fa fa-file-o"></i>',
        'textcard' => '<i class="fa fa-folder-o"></i>',
        'mpnews' => '<i class="fa fa-th-list"></i>',
        'sms' => '<i class="fa fa-file-text"></i>'
    ];
    const NODE_TYPES = [
        '根'  => ['color' => 'text-gray', 'type' => 'root', 'icon' => 'fa fa-sitemap'],
        '运营' => ['color' => 'text-blue', 'type' => 'company', 'icon' => 'fa fa-building'],
        '企业' => ['color' => 'text-green', 'type' => 'corp', 'icon' => 'fa fa-weixin'],
        '学校' => ['color' => 'text-purple', 'type' => 'school', 'icon' => 'fa fa-university'],
        '年级' => ['color' => 'text-black', 'type' => 'grade', 'icon' => 'fa fa-object-group'],
        '班级' => ['color' => 'text-black', 'type' => 'class', 'icon' => 'fa fa-users'],
        '其他' => ['color' => 'text-black', 'type' => 'other', 'icon' => 'fa fa-folder'],
    ];
    # 控制器相对路径
    const CONTROLLER_DIR = 'app/Http/Controllers';
    # 无需扫描的控制器
    const EXCLUDED_CONTROLLERS = [
        'Controller',
        'HomeController',
        'SyncController',
        'ApiController',
        'TestController',
    ];
    const CONTENT_TYPE = [
        'image' => 'image/*',
        'audio' => 'audio/*',
        'voice' => 'audio/*',
        'video' => 'video/*',
        'file'  => '*',
    ];
    const APPS = [
        'attendances'     => '考勤中心',
        'message_centers' => '消息中心',
        'score_centers'   => '成绩中心',
        'mobile_sites'    => '微网站',
        'home_works'      => '应用测试',
    ];
    # 错误代码 & 消息
    const WXERR = [
        -1      => '系统繁忙',
        0       => '请求成功',
        40001   => '不合法的secret参数',
        40003   => '无效的UserID',
        40004   => '不合法的媒体文件类型',
        40005   => '不合法的type参数',
        40006   => '不合法的文件大小',
        40007   => '不合法的media_id参数',
        40008   => '不合法的msgtype参数',
        40009   => '上传图片大小不是有效值',
        40011   => '上传视频大小不是有效值',
        40013   => '不合法的CorpID',
        40014   => '不合法的access_token',
        40016   => '不合法的按钮个数',
        40017   => '不合法的按钮类型',
        40018   => '不合法的按钮名字长度',
        40019   => '不合法的按钮KEY长度',
        40020   => '不合法的按钮URL长度',
        40022   => '不合法的子菜单级数',
        40023   => '不合法的子菜单按钮个数',
        40024   => '不合法的子菜单按钮类型',
        40025   => '不合法的子菜单按钮名字长度',
        40026   => '不合法的子菜单按钮KEY长度',
        40027   => '不合法的子菜单按钮URL长度',
        40029   => '不合法的oauth_code',
        40031   => '不合法的UserID列表',
        40032   => '不合法的UserID列表长度',
        40033   => '不合法的请求字符',
        40035   => '不合法的参数',
        40050   => 'chatid不存在',
        40054   => '不合法的子菜单url域名',
        40055   => '不合法的菜单url域名',
        40056   => '不合法的agentid',
        40057   => '不合法的callbackurl或者callbackurl验证失败',
        40058   => '不合法的参数',
        40059   => '不合法的上报地理位置标志位',
        40063   => '参数为空',
        40066   => '不合法的部门列表',
        40068   => '不合法的标签ID',
        40070   => '指定的标签范围结点全部无效',
        40071   => '不合法的标签名字',
        40072   => '不合法的标签名字长度',
        40073   => '不合法的openid',
        40074   => 'news消息不支持保密消息类型',
        40077   => '不合法的pre_auth_code参数',
        40078   => '不合法的auth_code参数',
        40080   => '不合法的suite_secret',
        40082   => '不合法的suite_token',
        40083   => '不合法的suite_id',
        40084   => '不合法的permanent_code参数',
        40085   => '不合法的的suite_ticket参数',
        40086   => '不合法的第三方应用appid',
        40088   => 'jobid不存在',
        40089   => '批量任务的结果已清理',
        40091   => 'secret不合法',
        40092   => '导入文件存在不合法的内容',
        40093   => '不合法的jsapi_ticket参数',
        40094   => '不合法的URL',
        41001   => '缺少access_token参数',
        41002   => '缺少corpid参数',
        41004   => '缺少secret参数',
        41006   => '缺少media_id参数',
        41008   => '缺少auth code参数',
        41009   => '缺少userid参数',
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
        42007   => 'pre_auth_code已过期',
        42009   => 'suite_access_token已过期',
        43004   => '指定的userid未绑定微信或未关注微信插件',
        44001   => '多媒体文件为空',
        44004   => '文本消息content参数为空',
        45001   => '多媒体文件大小超过限制',
        45002   => '消息内容大小超过限制',
        45004   => '应用description参数长度不符合系统限制',
        45007   => '语音播放时间超过限制',
        45008   => '图文消息的文章数量不符合系统限制',
        45009   => '接口调用超过限制',
        45022   => '应用name参数长度不符合系统限制',
        45024   => '帐号数量超过上限',
        45026   => '触发删除用户数的保护',
        45032   => '图文消息author参数长度超过限制',
        45033   => '接口并发调用超过限制',
        46003   => '菜单未设置',
        46004   => '指定的用户不存在',
        48002   => 'API接口无权限调用',
        48003   => '不合法的suite_id',
        48004   => '授权关系无效',
        48005   => 'API接口已废弃',
        50001   => 'redirect_url未登记可信域名',
        50002   => '成员不在权限范围',
        50003   => '应用已禁用',
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
        60103   => '手机号码不合法',
        60104   => '手机号码已存在',
        60105   => '邮箱不合法',
        60106   => '邮箱已存在',
        60107   => '微信号不合法',
        60110   => '用户所属部门数量超过限制',
        60111   => 'UserID不存在',
        60112   => '成员name参数不合法',
        60123   => '无效的部门id',
        60124   => '无效的父部门id',
        60125   => '非法部门名字',
        60127   => '缺少department参数',
        60129   => '成员手机和邮箱都为空',
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
        82002   => '不合法的PartyID列表长度',
        82003   => '不合法的TagID列表长度',
        84014   => '成员票据过期',
        84015   => '成员票据无效',
        84019   => '缺少templateid参数',
        84020   => 'templateid不存在',
        84021   => '缺少register_code参数',
        84022   => '无效的register_code参数',
        84023   => '不允许调用设置通讯录同步完成接口',
        84024   => '无注册信息',
        84025   => '不符合的state参数',
        85002   => '包含不合法的词语',
        85004   => '每企业每个月设置的可信域名不可超过20个',
        85005   => '可信域名未通过所有权校验',
        86001   => '参数 chatid 不合法',
        86003   => '参数 chatid 不存在',
        86004   => '参数 群名不合法',
        86005   => '参数 群主不合法',
        86006   => '群成员数过多或过少',
        86007   => '不合法的群成员',
        86008   => '非法操作非自己创建的群',
        86216   => '存在非法会话成员ID',
        86217   => '会话发送者不在会话成员列表中',
        86220   => '指定的会话参数不合法',
        90001   => '未认证摇一摇周边',
        90002   => '缺少摇一摇周边ticket参数',
        90003   => '摇一摇周边ticket参数不合法',
        90100   => '非法的对外属性类型',
        90101   => '对外属性：文本类型长度不合法',
        90102   => '对外属性：网页类型标题长度不合法',
        90103   => '对外属性：网页url不合法',
        90104   => '对外属性：小程序类型标题长度不合法',
        90105   => '对外属性：小程序类型pagepath不合法',
        90106   => '对外属性：请求参数不合法',
        91040   => '获取ticket的类型无效',
        301002  => '无权限操作指定的应用',
        301005  => '不允许删除创建者',
        301012  => '参数 position 不合法',
        301013  => '参数 telephone 不合法',
        301014  => '参数 english_name 不合法',
        301015  => '参数 mediaid 不合法',
        301016  => '上传语音文件不符合系统要求',
        301017  => '上传语音文件仅支持AMR格式',
        301021  => '参数 userid 无效',
        301022  => '获取打卡数据失败',
        301023  => 'useridlist非法或超过限额',
        301024  => '获取打卡记录时间间隔超限',
        301036  => '不允许更新该用户的userid',
        302003  => '批量导入任务的文件中userid有重复',
        302004  => '组织架构不合法（1不是一棵树，2 多个一样的partyid，3 partyid空，4 partyid name 空，5 同一个父节点下有两个子节点 部门名字一样 可能是以上情况，请一一排查）',
        302005  => '批量导入系统失败，请重新尝试导入',
        302006  => '批量导入任务的文件中partyid有重复',
        302007  => '批量导入任务的文件中，同一个部门下有两个子部门名字一样',
        2000002 => 'CorpId参数无效',
    ];
    
    # field names
    const USER_FIELDS = ['username', 'group_id', 'password', 'realname', 'gender', 'userid', 'position', 'enabled'];
    const EDUCATOR_FIELDS = ['user_id', 'school_id', 'sms_quote', 'enabled'];
    const STUDENT_FIELDS = [
        'user_id', 'class_id', 'student_number', 'card_number', 'oncampus', 'birthday', 'remark', 'enabled',
    ];
    const CUSTODIAN_FIELDS = ['user_id', 'enabled'];
    const MOBILE_FIELDS = ['user_id', 'mobile', 'isdefault', 'enabled'];
    const SCORE_FIELDS = ['student_id', 'subject_id', 'exam_id', 'class_rank', 'grade_rank', 'score', 'enabled'];
    const MESSAGE_FIELDS = [
        'comm_type_id', 'media_type_id', 'app_id', 'msl_id', 'title', 'content', 'serviceid', 'message_id',
        'url', 'media_ids', 's_user_id', 'r_user_id', 'message_type_id', 'read', 'sent'
    ];
    const EVENT_FIELDS = [
        'title', 'remark', 'location', 'contact', 'url', 'start', 'end', 'ispublic', 'iscourse', 'educator_id',
        'subject_id', 'alertable', 'alert_mins', 'user_id', 'enabled'
    ];
    const CS_FIELDS = ['custodian_id', 'student_id', 'relationship', 'enabled'];
    const DU_FIELDS = ['department_id', 'user_id', 'enabled'];
    const EC_FIELDS = ['educator_id', 'class_id', 'subject_id', 'enabled'];
    const SA_FIELDS = [
        'student_id', 'sas_id', 'punch_time', 'inorout', 'attendance_machine_id',
        'media_id', 'status', 'longitude', 'latitude'
    ];
    const MEMBER_FIELDS = [
        'userid', 'username', 'position', 'name', 'english_name',
        'mobile', 'email', 'department', 'gender', 'remark', 'enable'
    ];
}