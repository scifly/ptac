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

$factory->define(App\Models\Subject::class, function (Faker\Generator $faker) {

    return [
        'school_id' => 1 ,
        'name' => $faker->name,
        'isaux' => 1,
        'max_score' => 100,
        'pass_score' => 60,
        'grade_ids' =>'1|33|22',
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