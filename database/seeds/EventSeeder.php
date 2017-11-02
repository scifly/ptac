<?php

use Illuminate\Database\Seeder;

class EventSeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        factory(App\Models\Event::class, 6)->create()->each(function ($event) {
            $event->save();
        });
    }
}
