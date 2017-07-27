<?php

use Illuminate\Database\Seeder;

class ProcedureStepSeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        factory(App\Models\ProcedureStep::class, 5)->create()->each(function ($procedureStep) {
            $procedureStep->save();
        });
    }
}
