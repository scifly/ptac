<?php

use Illuminate\Database\Seeder;

class StudentAttendanceSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\Models\StudentAttendanceSetting::class, 20)->create()->each(function ($studentAttendanceSetting) {
            $studentAttendanceSetting->save();

        });
    }
}
