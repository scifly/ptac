<?php

use Illuminate\Database\Seeder;

class ProcedureSeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        factory(App\Models\Procedure::class, 5)->create()->each(function ($procedure) {
            $procedure->save();
        });
    }
}
