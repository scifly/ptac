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
$factory->define(App\User::class, function (Faker\Generator $faker) {
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
$factory->define(App\Models\School::class, function (Faker\Generator $faker) {

    return [
        'school_type_id' => 1 ,
        'name' => $faker->name,
        'address' =>  $faker->address,
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
