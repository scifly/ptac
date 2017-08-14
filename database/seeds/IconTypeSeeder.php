<?php

use Illuminate\Database\Seeder;

class IconTypeSeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        DB::table('icon_types')->insert([
            ['id' => 1, 'name' => '常用', 'remark' => '', 'enabled' => 1]
        ]);
    }
}
