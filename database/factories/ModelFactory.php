<?php

/*
 *
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

/** @var \Illuminate\Database\Eloquent\Factory $factory */
//$factory->define(App\Models\User::class, function (Faker\Generator $faker) {
//    static $password;
//
//    return [
//        'group_id' => 1 ,
//        'username' => $faker->name,
//        'email' => $faker->unique()->safeEmail,
//        'password' => $password ?: $password = bcrypt('secret'),
//        'gender' => rand(0,1),
//        'realname' => $faker->name,
//        'avatar_url' => "/image/001.jpg",
//        'enabled' => 1,
//        'userid' => uniqid('wx_'),
//
//    ];
//});

$factory->define(App\Models\SchoolType::class, function (Faker\Generator $faker) {
    
    return [
        'name' => $faker->name,
        'remark' => $faker->sentence(10),
        'enabled' => 1
    ];
    
});
$factory->define(App\Models\School::class, function (Faker\Generator $faker) {

    return [
        'school_type_id' => 1 ,
        'name' => $faker->name,
        'address' =>  $faker->address,
        'longitude' => 15.0244,
        'latitude' => 30.0244,
        'sms_max_cnt' => 30,
        'sms_used' => 10,
        'corp_id' => rand(1,10),
        'enabled' => 1
    ];

});

$factory->define(App\Models\Grade::class, function (Faker\Generator $faker) {

    return [
        'name' => $faker->name,
        'school_id' => 1,
        'educator_ids' => 'requestType',
        'enabled' => 1
    ];

});

$factory->define(App\Models\Educator::class, function (Faker\Generator $faker) {

    return [
        'user_id' => rand(1,5),
        'team_ids' => 'requestType',
        'school_id' => 1,
        'sms_quote' => 100,
        'enabled' => 1
    ];

});

$factory->define(App\Models\Company::class, function (Faker\Generator $faker) {

    return [
        'name' => $faker->company,
        'remark' => $faker->sentence(10),
        'corpid' => 'test1111',
        'enabled' => 1
    ];

});

$factory->define(App\Models\Turnstile::class, function (Faker\Generator $faker) {

    return [
        'name' => $faker->name,
        'location' => '成都武侯区',
        'school_id' => 1,
        'machineid' => '1456872587',
        'enabled' => 1
    ];

});

$factory->define(App\Models\ProcedureType::class, function (Faker\Generator $faker) {

    return [
        'name' => $faker->name,
        'remark' => 'Test',
        'enabled' => 1
    ];

});

$factory->define(App\Models\Tag::class, function (Faker\Generator $faker) {

    return [
        'name' => $faker->name,
        'enabled' => 1
    ];

});

// $factory->define(App\Models\ActionType::class, ActionTypeSeeder::class);
$factory->define(App\Models\FlowType::class, function (Faker\Generator $faker) {

    return [
        'procedure_type_id' => 1,
        'school_id' => 1,
        'name' => $faker->name,
        'remark' => $faker->name,
        'enabled' => 1
    ];

});

$factory->define(App\Models\Subject::class, function (Faker\Generator $faker) {
    $subject = ['语文','数学','英语','物理','化学','生物','政治','历史','地理','体育','音乐','美术',
        '高等数学','线性代数','计算机组成原理','计算机英语','大学英语','汇编','离散数学','心理咨询与辅导'
        ];
    $max= [100,150];
    $pass= [60,90];
    return [
        'school_id' => 1,
        'name' => $subject[rand(0,19)],
        'isaux' => 1,
        'max_score' => $max[rand(0,1)],
        'pass_score' => $pass[rand(0,1)],
        'grade_ids' => '1,33,22',
        'enabled' => 1

    ];
});

$factory->define(App\Models\User::class, function (Faker\Generator $faker) {

    return [
        'group_id' => 1,
        'username' => $faker->name,
        'password' => md5('123456'),
        'gender' => rand(0,1),
        'realname' => $faker->name,
        'avatar_url' => 'http://www.baidu.com',
        'remember_token' => '454564fdafdafadfsa',
        'email' => '18513094620@qq.com',
        'userid' => uniqid('wx_'),
        'enabled' => 1,
    ];
});


$factory->define(App\Models\Custodian::class, function (Faker\Generator $faker) {

    return [
        'user_id' => rand(1, 10),
        'expiry' => $faker->dateTime,
    ];
});

$factory->define(App\Models\Student::class, function (Faker\Generator $faker) {
    return [
        'user_id' => 1,
        'class_id' => 1,
        'sn' => 2017211132,
        'oncampus' => 1,
        'birthday' => $faker->date(),
        'remark' => '测试',
        'enabled' => 1
    ];
});


$factory->define(App\Models\Squad::class, function (Faker\Generator $faker) {

    return [
        'grade_id' => 1,
        'name' => $faker->name,
        'educator_ids' => 0035,
        'enabled' => 1
    ];

});


$factory->define(App\Models\ProcedureStep::class, function (Faker\Generator $faker) {

    return [
        'procedure_id' => 1,
        'name' => $faker->name,
        'approver_user_ids' => '1|2|3',
        'related_user_ids' => '2|3|4',
        'remark' => $faker->name,
        'enabled' => 1
    ];
});

$factory->define(App\Models\Group::class, function (Faker\Generator $faker) {

    return [
        'name' => $faker->name,
        'remark' => $faker->sentence(5),
        'enabled' => 1
    ];

});


$factory->define(App\Models\ScoreRange::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->name,
        'subject_ids' => '1|2|3',
        'school_id' => 1,
        'start_score' => 250,
        'end_score' => 400,
        'enabled' => 1
    ];
});

$factory->define(App\Models\Exam::class, function (Faker\Generator $faker) {

    return [
        'name' => $faker->name,
        'remark' => $faker->sentence(10),
        'exam_type_id' => 1,
        'class_ids' => '1,2,3',
        'subject_ids' => '1,2,3',
        'max_scores' => 150,
        'pass_scores' => 90,
        'start_date' => $faker->dateTime,
        'end_date' => $faker->dateTime,
        'enabled' => 1
    ];
});

$factory->define(App\Models\Flow::class, function (Faker\Generator $faker) {

    return [
        'initiator_user_id' => rand(1,5),
        'procedure_id' => rand(1,5),
        'procedure_step_id' => rand(1,5),
        'operator_user_id' => rand(1,5),
        'initiator_msg' => 'test',
        'initiator_media_ids' => '1|2',
        'operator_msg' => 'test',
        'operator_media_ids' => '2|3',
        'step_status' => rand(0,2),
    ];
});

$factory->define(App\Models\SubjectModule::class, function (Faker\Generator $faker) {

    return [
        'subject_id' => rand(1,10),
        'name' => $faker->name,
        'weight' => rand(1,5),
        'enabled' =>1

    ];
});

$factory->define(App\Models\ClassEducator::class, function (Faker\Generator $faker) {

    return [
        'educator_id' => rand(1,10),
        'class_id' => rand(1,10),
        'subject_id' => rand(1,10),
        'enabled' =>1

    ];
});


$factory->define(App\Models\CustodianStudent::class, function (Faker\Generator $faker) {
    $relationship = ['父子','父女','母子','母女'];
    return [
        'custodian_id' => rand(1, 10),
        'student_id' => rand(1, 10),
        'relationship' => $relationship[rand(0,3)],
        'enabled' =>1

    ];
});

$factory->define(App\Models\Event::class, function (Faker\Generator $faker) {

    return [
        'name' => $faker->name,
        'remark' => $faker->sentence(6),
        'location' => $faker->address,
        'contact' => $faker->name,
        'url' => $faker->url,
        'start' => $faker->dateTimeThisMonth(),
        'end' => $faker->dateTimeThisMonth(),
        'ispublic' => '0',
        'iscourse' => '0',
        'educator_id' => '1',
        'subject_id' => '1',
        'alertable' => '0',
        'alert_mins' => '5',
        'user_id' => '1',
        'enabled' => '0',
    ];
});

$factory->define(App\Models\Department::class, function (Faker\Generator $faker) {

    return [
        'parent_id' => rand(1, 10),
        'corp_id' => rand(1, 10),
        'school_id' => rand(1,20),
        'name' =>1,
        'remark' =>'测试',
        'order' =>rand(12121451,454612421),
        'enabled' =>rand(0,1)

    ];
});

$factory->define(App\Models\Corp::class, function (Faker\Generator $faker) {
    $company = [
        '北京中科软件有限公司', '北京华宇软件股份有限公司', '金蝶国际软件集团有限公司','成都智能软件公司', '润物软件技术有限公司',
        '北京希尔信息技术有限公司','亚太博大软件', '成都卓越精算软件有限责任公司', '冠群金辰软件公司', '福建富士通信息软件有限公司',
        '北京有生博大软件技术有限公司', '云南保会通软件公司', '西安博达软件有限公司', '上海启明软件股份有限公司', '深圳市伊登软件有限公司',
        '厦门搜企软件有限公司', '北京日桥信息技术有限公司', '南京橙红信息科技有限公司'
];
    return [
        'name' => $company[rand(0, 17)],
        'corpid' => strtolower(str_random(18)),
        'enabled' =>rand(0,1),
        'company_id' =>rand(1,10),

    ];
});

$factory->define(App\Models\EducatorAttendanceSetting::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->name,
        'school_id' => rand(1,20),
        'start' =>date('Y-m-d H:i:s',time()),
        'end' => date('Y-m-d H:i:s',strtotime("+8 hours")),
        'direction' =>rand(0,1),

    ];
});


$factory->define(App\Models\StudentAttendanceSetting::class, function (Faker\Generator $faker) {
    $day = ['星期一','星期二','星期三','星期四','星期五','星期六','星期日'];
    return [
        'name' => $faker->name,
        'grade_id' => rand(1,20),
        'semester_id' =>rand(1,6),
        'ispublic' =>1,
        'start' =>date('Y-m-d H:i:s',time()),
        'end' => date('Y-m-d H:i:s',strtotime("+8 hours")),
        'day'=>$day[rand(0,6)],
        'direction' =>rand(0,1),
        'msg_template'=>$faker->name
    ];
});


$factory->define(App\Models\Semester::class, function (Faker\Generator $faker) {
    $name = ['第一学期','第二学期','第三学期','第四学期','第五学期','第六学期'];
    return [
        'school_id' => rand(1,20),
        'name' =>$name[rand(0,5)],
        'remark' =>1,
        'start_date' =>date('Y-m-d H:i:s',time()),
        'end_date' => date('Y-m-d H:i:s',strtotime("+4 month")),
        'enabled' =>rand(0,1),

    ];
});

$factory->define(App\Models\Major::class, function (Faker\Generator $faker) {
    $name = ['法学','环境科学','环境工程','计算机科学与技术','网络工程',
        '软件工程','通信工程','物联网工程','地理科学', '人文地理与城乡规划',
        '自然地理与资源环境','地理信息科学','测绘工程','化学应用化学科学教育', '教育学心理学',
        '教育技术学','历史学经济学', '国际经济与贸易市场','营销专业','工商管理会计学',
    '数学与应用数学','信息与计算科学','信息管理与信息系统', '统计学','体育教育'];
    return [
        'name' =>$name[rand(0,24)],
        'remark' =>'测试',
        'school_id' =>rand(1,5),
        'enabled' =>rand(0,1),

    ];
});

$factory->define(App\Models\MajorSubject::class, function (Faker\Generator $faker) {
    return [
        'major_id' =>rand(1,20),
        'subject_id' =>rand(1,20),

    ];
});

$factory->define(App\Models\Mobile::class, function (Faker\Generator $faker) {
    $mobile = [
        '13408393001','13408393002','13408393003', '13408393004','13408393005',
        '13408393006','13408393007','13408393008','13408393009', '13408393010',
    ];
    return [
        'user_id' =>rand(1,20),
        'mobile' =>$mobile[rand(0,9)],
        'enabled' => 1,
        'isdefault' => 1

    ];
});

$factory->define(App\Models\DepartmentUser::class, function (Faker\Generator $faker) {
    return [
        'department_id' =>rand(0,19),
        'user_id' =>rand(0,19),
        'enabled' => 1,

    ];
});
