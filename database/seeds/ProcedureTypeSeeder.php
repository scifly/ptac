<?php

use Illuminate\Database\Seeder;

class ProcedureTypeSeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        factory(App\Models\ProcedureType::class, 5)->create()->each(function ($procedure_type) {
            $procedure_type->save();
        });
    }
}
