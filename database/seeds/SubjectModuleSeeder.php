<?php

use Illuminate\Database\Seeder;

class SubjectModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\Models\SubjectModule::class, 10)->create()->each(function ($subjectModule) {
            $subjectModule->save();
        });
    }
}
