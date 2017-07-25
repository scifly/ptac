<?php

use Illuminate\Database\Seeder;

class AttendanceMachineSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        factory(App\Models\AttendanceMachine::class, 5)->create()->each(function ($attendanceMachine) {
            $attendanceMachine->save();
        });

    }
}
