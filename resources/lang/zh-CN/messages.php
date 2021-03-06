<?php
return [
    'ok'                       => '操作成功',
    'fail'                     => '操作失败',
    'created'                  => '创建成功',
    'edit_ok'                  => '保存成功',
    'del_ok'                   => '删除成功',
    'create_fail'              => '创建失败',
    'file_uploaded'            => '文件上传成功',
    'file_upload_failed'       => '文件上传失败',
    'empty_file'               => '文件为空或格式错误',
    'incorrect_data_range'     => '起始日期不得晚于截止日期',
    'edit_fail'                => '保存失败',
    'del_fail'                 => '删除失败',
    'bad_request'              => '请求错误',
    'unauthorized'             => '您无权访问该页面',
    'forbidden'                => '禁止访问',
    'not_found'                => '找不到需要访问的对象/页面',
    'school_not_found'         => '没有这所学校',
    'not_acceptable'           => '参数错误',
    'method_not_allowed'       => '不支持该请求方法',
    'import_started'           => '文件上传成功，导入开始...',
    'export_started'           => '已提交记录导出请求，请稍候...',
    'import_succeeded'         => '导入成功',
    'import_request_submitted' => '已提交导入请求<br />需要新增的记录数：%s<br />需要更新的记录数：%s',
    'import_illegals'          => '<br />非法记录数: %s, 请参考导出文件',
    'empty_import_file'        => '没有数据可以导入',
    'internal_server_error'    => '内部服务器错误',
    'invalid_credentials'      => '用户名/密码错误',
    'invalid_argument'         => '参数无效',
    'invalid_file_format'      => '数据格式错误',
    'invalid_data_format'      => '导入数据格式不正确',
    'token_mismatch'           => '页面已失效，请重试',
    'synced'                   => '同步成功',
    'sync_failed'              => '部分用户同步失败，请参考导出的excel文件',
    'operator'                 => [
        'stored' => '保存超级管理员',
    ],
    'action'                   => [
        'title'         => '功能',
        'not_found'     => '没有这个功能',
        'misconfigured' => '功能配置错误',
    ],
    'app'                      => [
        'title'      => '应用管理',
        'configured' => '应用设置成功',
        'not_found'  => '找不到指定的应用',
        'submitted'  => '已提交应用设置请求',
    ],
    'article'                  => [
        'title' => '微网站文章',
    ],
    'class'                    => [
        'title'              => '班级管理',
        'not_found'          => '未找到该班级的相关信息',
        'no_related_classes' => '您尚未绑定任何班级',
    ],
    'column'                   => [
        'title' => '微网站栏目',
    ],
    'custodian'                => [
        'title'            => '监护人',
        'not_found'        => '没有这个监护人',
        'export_completed' => '监护人导出完毕，请打开下载的excel文件查看详情',
    ],
    'department'               => [
        'title'             => '部门管理',
        'department_sync'   => '%s部门',
        'has_children'      => '请先删除子部门',
        'not_found'         => '没有这个部门',
        'request_submitted' => '已提交部门删除请求',
        'deleted'           => '部门已删除，请重新加载部门树查看操作结果',
        'forbidden'         => '请删除该部门对应的运营/企业/学校/年级/班级对象'
    ],
    'educator'                 => [
        'title'                     => '教职员工',
        'not_found'                 => '没有这个教职员工',
        'import_failed'             => '批量导入失败',
        'import_validation_error'   => '学校/部门名称错误',
        'import_completed'          => '教职员工导入完毕',
        'export_completed'          => '教职员工导出完毕，请打开下载的excel文件查看详情',
        'switch_school_not_allowed' => '不得切换学校',
        'role_nonexistent'          => '基本角色不存在',
    ],
    'face'                     => [
        'title'            => '人脸识别设置',
        'config_started'   => '已提交人脸识别设置请求...',
        'config_completed' => '设置成功',
        'config_failed'    => '人脸识别设置失败：%s',
        'detail_not_found' => '获取人脸信息失败',
    ],
    'grade'                    => [
        'title'     => '年级管理',
        'not_found' => '未找到该年级的相关信息',
    ],
    'group'                    => [
        'title'     => '角色/权限',
        'not_found' => '没有这个角色',
    ],
    'menu'                     => [
        'title'         => '菜单管理',
        'has_children'  => '请先删除子菜单',
        'misconfigured' => '菜单配置错误',
        'forbidden'     => '请删除该菜单对应的运营/企业/学校对象'
    ],
    'message'                  => [
        'title'            => '消息',
        'not_found'        => '没有这条消息',
        'sent'             => '已发：%s (成功：%s, 失败：%s)<br />微信：%s (成功: %s, 失败: %s)<br />短信：%s (成功: %s, 失败: %s)<br />请点击“已发送”卡片查看详情',
        'failed'           => '消息发送失败',
        'uploaded'         => '文件上传成功',
        'sms_send_failed'  => '短信推送失败',
        'invalid_app_list' => '应用不存在，请检查后重试',
        'invalid_corp'     => '企业号不存在，请检查后重试',
        'empty_targets'    => '请选择发送对象',
        'submitted'        => '已提交消息发送请求',
        'preview'          => '消息预览已发送至你的手机微信，请打开相关应用查看',
    ],
    'module'                   => [
        'title' => '应用模块',
    ],
    'passage_log'              => [
        'title'     => '通行记录',
        'submitted' => '已发送采集请求。数据采集完成后请重新加载当前页面',
        'gathered'  => '采集完毕',
    ],
    'school'                   => [
        'title'        => '学校管理',
        'not_found'    => '未找到该学校的相关信息',
        'corp_changed' => '请先删除此学校所有相关数据(部门、部门用户绑定关系、角色、微网站等)，并在新的企业微信下创建该学校',
        'menu_created' => '学校后台管理菜单创建成功',
    ],
    'score'                    => [
        'title'                   => '成绩中心',
        'not_found'               => '找不到指定学生、科目及考试对应的成绩/总成绩',
        'zero_classes'            => '您尚未绑定任何班级',
        'unauthorized_stat'       => '您无权进行该成绩分析',
        'exam_not_found'          => '找不到需要导入成绩对应的考试',
        'import_completed'        => '成绩导入完毕',
        'message_template'        => '尊敬的%s家长, %s考试成绩已出: %s。',
        'message_send_result'     => '成功: %s条; <br />失败: %s条',
        'total_score_unavailable' => '请先统计总分',
        'student_class_mismatch'  => '该学生不在此班级',
        'send_request_submitted'  => '已提交成绩发送请求',
    ],
    'semester'                 => [
        'title'     => '学期设置',
        'not_found' => '学期信息有误',
    ],
    'student'                  => [
        'title'                   => '学籍管理',
        'not_found'               => '未找到该学生的相关信息',
        'import_validation_error' => '学校/年级/班级名称错误',
        'import_completed'        => '学籍导入完毕',
        'export_completed'        => '学籍导出完毕，请打开下载的excel文件查看详情',
    ],
    'student_attendance'       => [
        'title'              => '学生考勤',
        'not_found'          => '未找到该学生的考勤记录',
        'not_available'      => '指定班级所属年级未设置考勤规则！',
        'authenticated'      => '验证成功',
        'weekday_mismatched' => '请选择和规则对应的星期！',
    ],
    'sas'                      => [
        'title'     => '学生考勤规则',
        'not_found' => '没有找到相关的考勤规则',
    ],
    'sms_charge'               => [
        'title'        => '短信充值',
        'insufficient' => '(上级)余额不足',
    ],
    'tab'                      => [
        'title'     => '控制器(卡片)',
        'not_found' => '没有这个控制器',
    ],
    'template'                 => [
        'title'     => '消息模板',
        'started'   => '已提交消息模板下载请求...',
        'completed' => '消息模板下载完成',
        'failed'    => '操作失败',
        'not_found' => '找不到这个模板',
    ],
    'turnstile'                => [
        'title'     => '门禁管理',
        'not_found' => '该门禁没有注册',
    ],
    'user'                     => [
        'not_found'  => '没有这个用户',
        'vericode'   => '注册验证码：',
        'v_invalid'  => '验证码无效',
        'registered' => '注册成功',
        'v_sent'     => '验证码已发送，请在30分钟内输入',
        'v_failed'   => '短信发送失败',
    ],
    'wap'                      => [
        'title' => '微网站',
    ],
];
