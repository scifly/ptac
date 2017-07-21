<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        
        // $this->call(UsersTableSeeder::class);
//        $this->call(SchoolTypeSeeder::class);
//        $this->call(CorpSeeder::class);
     //   $this->call(SubjectSeeder::class);
        $this->call(SchoolSeeder::class);
    }
    
}
