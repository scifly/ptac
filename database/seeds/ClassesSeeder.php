<?php

use Illuminate\Database\Seeder;

class ClassesSeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        factory(App\Models\Squad::class, 20)->create()->each(function ($user) {
            $user->save();
        });
    }
}
