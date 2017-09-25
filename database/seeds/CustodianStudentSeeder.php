<?php

use Illuminate\Database\Seeder;

class CustodianStudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\Models\CustodianStudent::class, 10)->create()->each(function ($custodianStudent) {
            $custodianStudent->save();
        });
    }
}
