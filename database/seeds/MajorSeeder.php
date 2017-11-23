<?php

use Illuminate\Database\Seeder;

class MajorSeeder extends Seeder {
    
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        factory(App\Models\Major::class, 10)->create()->each(function ($major) {
            $major->save();
        });
    }
}
