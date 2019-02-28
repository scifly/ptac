<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages.
    |
    */

    'accepted'             => ':attribute 必须接受',
    'active_url'           => '网址 :attribute 无效',
    'after'                => ':attribute 必须晚于 :date',
    'after_or_equal'       => ':attribute 必须等于 :date 或更晚',
    'alpha'                => ':attribute 只能由字母组成',
    'alpha_dash'           => ':attribute 只能由字母、数字和斜杠组成',
    'alpha_num'            => ':attribute 只能由字母和数字组成',
    'array'                => ':attribute 必须是数组',
    'before'               => ':attribute 必须早于 :date',
    'before_or_equal'      => ':attribute 必须等于 :date 或更早',
    'between'              => [
        'numeric' => ':attribute 必须介于 :min - :max 之间',
        'file'    => ':attribute 必须介于 :min - :max kb 之间',
        'string'  => ':attribute 必须介于 :min - :max 个字符之间',
        'array'   => ':attribute 只能包含 :min - :max 个值',
    ],
    'boolean'              => ':attribute 必须为布尔值',
    'confirmed'            => ':attribute 两次输入不一致',
    'date'                 => '日期 :attribute 无效',
    'date_format'          => ':attribute 的格式必须为 :format',
    'different'            => ':attribute 和 :other 必须不同',
    'digits'               => ':attribute 必须是 :digits 位的数字',
    'digits_between'       => ':attribute 必须是介于 :min 和 :max 位的数字',
    'dimensions'           => ':attribute 图片尺寸不正确',
    'distinct'             => ':attribute 已经存在',
    'email'                => '邮箱 :attribute 格式不正确',
    'exists'               => ':attribute 不存在',
    'file'                 => ':attribute 必须是文件',
    'filled'               => ':attribute 不能为空',
    'image'                => ':attribute 必须是图片',
    'in'                   => '已选的属性 :attribute 非法',
    'in_array'             => ':attribute 不在 :other 中',
    'integer'              => ':attribute 必须是整数',
    'ip'                   => 'IP 地址 :attribute 无效',
    'ipv4'                 => 'IPv4 地址 :attribute 无效',
    'ipv6'                 => 'IPv6 地址 :attribute 无效 ',
    'json'                 => 'JSON 格式 :attribute 错误',
    'max'                  => [
        'numeric' => ':attribute 不能大于 :max',
        'file'    => ':attribute 不能大于 :max kb',
        'string'  => ':attribute 不能大于 :max 个字符',
        'array'   => ':attribute 最多只能包含 :max 个值',
    ],
    'mimes'                => ':attribute 必须是一个 :values 类型的文件',
    'mimetypes'            => ':attribute 必须是一个 :values 类型的文件',
    'min'                  => [
        'numeric' => ':attribute 必须大于等于 :min',
        'file'    => ':attribute 大小不能小于 :min kb',
        'string'  => ':attribute 至少为 :min 个字符',
        'array'   => ':attribute 至少应包含 :min 个值',
    ],
    'not_in'               => '已选的属性 :attribute 非法',
    'numeric'              => ':attribute必须是一个数字',
    'present'              => ':attribute必须存在',
    'regex'                => ':attribute格式不正确',
    'required'             => ':attribute不能为空',
    'required_if'          => '当 :other 为 :value 时 :attribute 不能为空',
    'required_unless'      => '当 :other 不为 :value 时 :attribute 不能为空',
    'required_with'        => '当 :values 存在时 :attribute 不能为空',
    'required_with_all'    => '当 :values 存在时 :attribute 不能为空',
    'required_without'     => '当 :values 不存在时 :attribute 不能为空',
    'required_without_all' => '当 :values 都不存在时 :attribute 不能为空',
    'same'                 => ':attribute 和 :other 必须相同',
    'size'                 => [
        'numeric' => ':attribute 大小必须为 :size',
        'file'    => ':attribute 大小必须为 :size kb',
        'string'  => ':attribute 必须是 :size 个字符',
        'array'   => ':attribute 必须包含 :size 个值',
    ],
    'string'               => ':attribute 必须是一个字符串',
    'timezone'             => ':attribute 必须是一个合法的时区值',
    'unique'               => ':attribute已经存在',
    'uploaded'             => ':attribute 上传失败',
    'url'                  => ':attribute 格式不正确',
    'grade_id'             => ':attribute 必须的',
    'greater_than'         => ':attribute 必须大于 :other',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention 'attribute.rule' to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom'               => [
        'avatar_url' => [
            'url' => 'users avatar_url is not correct',
        ],
        'sn' => [
            'unique' => ':attribute不唯一',
        ],
        'student_ids'=>[
            'required'=>'请先设置:attribute'
        ],
        'subject_ids'=>[
            'required'=>':attribute 是必须的'
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap attribute place-holders
    | with something more reader friendly such as E-Mail Address instead
    | of 'email'. This simply helps us make messages a little cleaner.
    |
    */

    'attributes'           => [
        'name'                  => '名称',
        'english_name'          => '英文名',
        'sn'                    => '学号',
        'discount'              => '折扣(discount)',
        'machineid'             => '考勤机id',
        'username'              => '用户名',
        'email'                 => '邮箱',
        'first_name'            => '名字',
        'last_name'             => '姓氏',
        'password'              => '密码',
        'password_confirmation' => '确认密码',
        'city'                  => '城市',
        'country'               => '国家',
        'address'               => '地址',
        'phone'                 => '电话',
        'mobile'                => '手机',
        'age'                   => '年龄',
        'sex'                   => '性别',
        'gender'                => '性别',
        'day'                   => '天',
        'month'                 => '月',
        'year'                  => '年',
        'hour'                  => '时',
        'minute'                => '分',
        'second'                => '秒',
        'title'                 => '标题',
        'content'               => '内容',
        'description'           => '描述',
        'excerpt'               => '摘要',
        'date'                  => '日期',
        'time'                  => '时间',
        'available'             => '可用的',
        'size'                  => '大小',
        'enabled'               => '状态',
        'selectedDepartments'   => '所属部门',
        'student_ids'           => '被监护人',
        'subject_ids'           => '科目',
        'grade_id'              => '年级',

        'actionId'              => '卡片/功能权限',
        // 'tabId'              => '卡片/功能权限',
        'remark'                => '备注',
        'menu_ids'              => '菜单权限',
        'user.email'            => '电子邮箱',
        'start_date'            => '开始日期',
        'end_date'              => '结束日期',
        'start_score'           => '起始分数',
        'end_score'             => '截止分数'
    ],
];
