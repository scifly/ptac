<?php

use Illuminate\Database\Seeder;

class SchoolTypeSeeder extends Seeder {
    
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
    
        factory(App\Models\SchoolType::class, 5)->create()->each(function ($schoolType) {
            $schoolType->save();
        });
        
    }
}
