<?php

/*
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
        'educator_ids' => 'abc',
        'enabled' => 1
    ];

});

$factory->define(App\Models\Educator::class, function (Faker\Generator $faker) {

    return [
        'user_id' => 1,
        'team_ids' => 'abc',
        'school_id' => 1,
        'sms_quote' => 100,
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

$factory->define(App\Models\Procedure::class, function (Faker\Generator $faker) {

    return [
        'procedure_type_id' => 1,
        'school_id' => 1,
        'name' => $faker->name,
        'remark' => $faker->name,
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
        'avatar_url' =>'http://www.baidu.com',
        'remember_token' => '454564fdafdafadfsa',
        'email' => '18513094620@qq.com',
        'wechatid' => 'fdsfds45454',
        'enabled' => 1,
    ];

});


$factory->define(App\Models\Custodian::class, function (Faker\Generator $faker) {

    return [
        'user_id' => rand(1,5),
        'expiry' => $faker->dateTime,
    ];

});

$factory->define(App\Models\ProcedureStep::class, function (Faker\Generator $faker) {

    return [
        'procedure_id' => 1,
        'name' => $faker->name,
        'approver_user_ids'=>'1|2|3',
        'related_user_ids' => '2|3|4',
        'remark' => $faker->name,
        'enabled' => 1
    ];

});



