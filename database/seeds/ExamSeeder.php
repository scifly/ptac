<?php

use Illuminate\Database\Seeder;

class ExamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\Models\Exam::class, 10)->create()->each(function ($exam) {
            $exam->save();
        });
    }
}
