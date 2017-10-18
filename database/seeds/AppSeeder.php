<?php

use Illuminate\Database\Seeder;

class AppSeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        factory(App\Models\App::class, 5)->create()->each(function ($app) {
            $app->save();
        });
    }
}
