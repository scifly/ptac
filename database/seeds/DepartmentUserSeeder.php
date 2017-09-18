<?php

use Illuminate\Database\Seeder;

class DepartmentUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\Models\DepartmentUser::class, 20)->create()->each(function ($department) {
            $department->save();
        });
    }
}
