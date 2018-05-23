<?php

return [
    'ok' => '操作成功',
    'fail' => '操作失败',
    'created' => '创建成功',
    'edit_ok' => '保存成功',
    'del_ok' => '删除成功',
    'create_fail' => '创建失败',
    'edit_fail' => '保存失败',
    'del_fail' => '删除失败',
    'bad_request' => '请求错误',
    'unauthorized' => '您无权访问该页面',
    'forbidden' => '禁止访问',
    'not_found' => '找不到需要访问的页面',
    'not_acceptable' => '参数错误',
    'method_not_allowed' => '不支持该请求方法',
    'internal_server_error' => '内部服务器错误',
    'nonexistent_action' => '功能不存在',
    'misconfigured_action' => '功能配置错误',
    'invalid_credentials' => '用户名/密码错误',
    'invalid_argument' => '参数无效',
    'token_mismatch' => '页面已失效，请重试',
    'student_not_found' => '未找到该学生的相关信息',
    'class_not_found' => '未找到该班级的相关信息',
    'grade_not_found' => '未找到该年级的相关信息',
    'school_not_found' => '未找到该学校的相关信息',
    'semester_not_found' => '学期信息有误',
    'machine_not_found' => '该考勤机没有注册',
    'user_not_found' => '没有这个用户',
    'educator_not_found' => '没有这个教职员工',
    'wechat_synced' => '已同步到企业微信',
    'operator' => [
        'stored' => '保存超级管理员'
    ],
    'app' => [
        'title' => '企业应用管理',
        'app_configured' => '应用设置成功'
    ],
    'department' => [
        'title' => '部门管理',
        'department_sync' => '%s企业微信部门',
        'has_children' => '请先删除自部门',
    ],
    'menu' => [
        'title' => '菜单管理',
        'has_children' => '请先删除子菜单'
    ],
    'message' => [
        'title' => '消息中心',
        'sent' => '已发消息数量：%s<br />发送成功：%s<br />发送失败：%s<br />请点击已发送卡片查看详情。',
        'failed' => '消息发送失败',
        'uploaded' => '文件上传成功',
        'sms_send_failed' => '短信推送失败',
        'invalid_app_list' => '应用不存在，请检查后重试',
        'invalid_corp' => '企业号不存在，请检查后重试',
        'empty_targets' => '请选择发送对象',
        'submitted' => '已提交消息发送请求',
    ],
    'school' => [
        'title' => '学校管理',
        'corp_changed' => '请先删除此学校所有相关数据(部门、部门用户绑定关系、角色、微网站等)，并在新的企业微信下创建该学校'
    ],
    'score' => [
        'title' => '成绩中心',
        'zero_classes' => '您尚未绑定任何班级',
        'unauthorized_stat' => '您无权进行该成绩分析'
    ],
    'student' => [
        'title' => '学籍管理',
        'invalid_file_format' => '文件格式错误',
        'invalid_data_format' => '导入数据格式不正确',
        'import_request_submitted' => '已提交导入请求\n需要新增的学籍数：%s\n需要更新的学籍数：%s\n非法学籍数: %s, 请参考导出文件',
        'empty_import_file' => '没有数据可以导入'
    ],
];
