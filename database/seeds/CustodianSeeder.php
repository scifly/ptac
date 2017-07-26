<?php

use Illuminate\Database\Seeder;

class CustodianSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\Models\Custodian::class, 5)->create()->each(function ($custodian) {
            $custodian->save();
        });
    }
}
