<?php

use Illuminate\Database\Seeder;

class EducatorSeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        //
        factory(App\Models\Educator::class, 10)->create()->each(function ($educator) {
            $educator->save();
        });
    }
}
