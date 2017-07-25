<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        
//         $this->call(UserSeeder::class);
//        $this->call(SchoolTypeSeeder::class);
//        $this->call(CorpSeeder::class);
//        $this->call(SchoolSeeder::class);
//        $this->call(GradeSeeder::class);
//        $this->call(EducatorSeeder::class);
        $this->call(CompaniesTableSeeder::class);
    }
    
}
