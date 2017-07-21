;<?php

use Illuminate\Database\Seeder;

class CorpSeeder extends Seeder {
    
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        
        DB::table('corps')->insert([
            ['id' => 1, 'name' => '成都凌凯通信技术', 'corpid' => 'wxd529dfabd93ece93', 'enabled' => 1],
            ['id' => 2, 'name' => '四川盛世华唐', 'corpid' => 'wxbb64d363dbf31792', 'enabled' => 1]
        ]);

    }
}
