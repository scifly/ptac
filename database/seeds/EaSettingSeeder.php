<?php

use Illuminate\Database\Seeder;

class EaSettingSeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        factory(App\Models\EducatorAttendanceSetting::class, 20)->create()->each(function ($EaSetting) {
            $EaSetting->save();
        });
    }
}
