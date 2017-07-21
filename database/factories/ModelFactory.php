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
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'password' => $password ?: $password = bcrypt('secret'),
        'remember_token' => str_random(10),
    ];
});

$factory->define(App\Models\SchoolType::class, function (Faker\Generator $faker) {
    
    return [
        'name' => $faker->name,
        'remark' => $faker->sentence(10),
        'enabled' => 1
    ];
    
});

$factory->define(App\Models\AttendanceMachine::class, function (Faker\Generator $faker) {

    return [
        'name' => $faker->name,
        'location' => $faker->address,
        'school_id' => 1,
        'machineid' => 'fdsafdsaf454',
        'enabled' => 1
    ];

});

$factory->define(App\Models\School::class, function (Faker\Generator $faker) {

    return [
        'school_type_id' => 1,
        'name' => $faker->name,
        'address' => $faker->address,
        'longitude' => 12.57454,
        'latitude' => 36.15587,
        'corp_id' => 1,
        'sms_max_cnt' => 1,
        'sms_used' => 1,
        'enabled' => 1
    ];

});