<?php

use Illuminate\Database\Seeder;

class MajorSubjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\Models\MajorSubject::class, 10)->create()->each(function ($majorSubject) {
            $majorSubject->save();
        });
    }
}
