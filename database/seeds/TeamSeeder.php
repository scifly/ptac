<?php

use Illuminate\Database\Seeder;

class TeamSeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {

        factory(App\Models\Team::class, 15)->create()->each(function ($team) {
            $team->save();
        });

    }
}
