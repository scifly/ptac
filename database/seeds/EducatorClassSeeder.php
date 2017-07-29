<?php

use Illuminate\Database\Seeder;

class EducatorClassSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\Models\EducatorClass::class, 10)->create()->each(function ($educatorClass) {
            $educatorClass->save();

        });
    }
}
