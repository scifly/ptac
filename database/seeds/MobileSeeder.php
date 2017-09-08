<?php

use Illuminate\Database\Seeder;

class MobileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\Models\Mobile::class, 20)->create()->each(function ($mobile) {
            $mobile->save();
        });
    }
}
