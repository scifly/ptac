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
$factory->define(App\Models\Team::class, function (Faker\Generator $faker) {

    return [
        'name' => $faker->name,
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


$factory->define(App\Models\Subject::class, function (Faker\Generator $faker) {

    return [
        'school_id' => 1,
        'name' => $faker->name,
        'isaux' => 1,
        'max_score' => 150,
        'pass_score' => 90,
        'grade_ids' => '1|33|22',
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
        'enabled' => 1,
        'userid' => 002,
        'department_ids'=>30

    ];

});

$factory->define(App\Models\Subject::class, function (Faker\Generator $faker) {

    return [
        'school_id' => 1,
        'name' => $faker->name,
        'isaux' => 1,
        'max_score' => 150,
        'pass_score' => 90,
        'grade_ids' => '1|33|22'

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
    ];
});


$factory->define(App\Models\Squad::class, function (Faker\Generator $faker) {

    return [
        'grade_id' => 1,
        'name' => $faker->name,
        'educator_ids' => 0035,
        'enabled' => 1,

    ];

});

$factory->define(App\Models\Group::class, function (Faker\Generator $faker) {

    return [
        'name' => $faker->name,
        'remark' => $faker->sentence(5),
        'enabled' => 1,

    ];

});


$factory->define(App\Models\Student::class, function (Faker\Generator $faker) {

    return [
        'user_id' => 1,
        'class_id' => 1,
        'student_number' => $faker->randomNumber(9),
        'card_number' => $faker->creditCardNumber,
        'oncampus' => 1,
        'birthday' => $faker->date(),
        'remark' => $faker->sentence(10)
    ];
});

$factory->define(App\Models\Exam::class, function (Faker\Generator $faker) {

    return [
        'name' => $faker->name,
        'remark' => $faker->sentence(10),
        'exam_type_id' => 1,
        'class_ids' => '1|2|3',
        'subject_ids' => '1|2|3',
        'max_scores' => 150,
        'pass_scores' => 90,
        'start_date' => $faker->dateTime,
        'end_date' => $faker->dateTime,
        'enabled' => 1
    ];
});





