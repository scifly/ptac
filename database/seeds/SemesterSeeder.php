<?php

use Illuminate\Database\Seeder;

class SemesterSeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        factory(App\Models\Semester::class, 20)->create()->each(function ($semester) {
            $semester->save();

        });
    }
}
