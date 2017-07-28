<?php

use Illuminate\Database\Seeder;

class ProcedureLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\Models\ProcedureLog::class, 5)->create()->each(function ($procedureLog) {
            $procedureLog->save();
        });
    }
}
