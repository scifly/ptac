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
$factory->define(App\Models\User::class, function (Faker\Generator $faker) {
    static $password;

    return [
        'group_id' => 1 ,
        'username' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'password' => $password ?: $password = bcrypt('secret'),
        'gender' => 1,
        'realname' => $faker->name,
        'avatar_url' => "/image",
        'enabled' => 1,
        'userid' => 1,
        'department_ids' => "1,2",

    ];
});

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
        'corp_id' => 1,
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

$factory->define(App\Models\AttendanceMachine::class, function (Faker\Generator $faker) {

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

$factory->define(App\Models\Team::class, function (Faker\Generator $faker) {

    return [
        'name' => $faker->name,
        'enabled' => 1
    ];

});

// $factory->define(App\Models\ActionType::class, ActionTypeSeeder::class);
$factory->define(App\Models\Procedure::class, function (Faker\Generator $faker) {

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
        'gender' => 1,
        'realname' => $faker->name,
        'avatar_url' => 'http://www.baidu.com',
        'remember_token' => '454564fdafdafadfsa',
        'email' => '18513094620@qq.com',
        'wechatid' => 'fdsfds45454',
        'userid' => rand(1,5).",".rand(5,10),
        'department_ids' =>rand(1,5).",".rand(5,10),
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
        'student_number' => 2017211132,
        'card_number' => $faker->creditCardNumber,
        'oncampus' => 1,
        'birthday' => $faker->date(),
        'remark' => $faker->sentence(10),
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

$factory->define(App\Models\ProcedureLog::class, function (Faker\Generator $faker) {

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

$factory->define(App\Models\EducatorClass::class, function (Faker\Generator $faker) {

    return [
        'educator_id' => rand(1,10),
        'class_id' => rand(1,10),
        'subject_id' => rand(1,10),
        'enabled' =>1

    ];
});


$factory->define(App\Models\CustodianStudent::class, function (Faker\Generator $faker) {

    return [
        'custodian_id' => rand(1, 10),
        'student_id' => rand(1, 10),
        'relationship' => '父子',
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
        'corp' => rand(1, 10),
        'school_id' => '父子',
        'name' =>1,
        'remark' =>'测试',
        'order' => $faker->creditCardNumber,
        'enabled' =>rand(0,1)

    ];
});



