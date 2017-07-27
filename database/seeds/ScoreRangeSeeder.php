<?php

use Illuminate\Database\Seeder;

class ScoreRangeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\Models\ScoreRange::class, 5)->create()->each(function ($scoreRange) {
            $scoreRange->save();
        });
    }
}
