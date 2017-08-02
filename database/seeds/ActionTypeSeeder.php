<?php

use Illuminate\Database\Seeder;

class ActionTypeSeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        DB::table('action_types')->insert([
            ['id' => 1, 'name' => 'GET', 'enabled' => 1],
            ['id' => 2, 'name' => 'POST', 'enabled' => 1],
            ['id' => 3, 'name' => 'PUT', 'enabled' => 1],
            ['id' => 4, 'name' => 'DELETE', 'enabled' => 1],
            ['id' => 5, 'name' => 'HEAD', 'enabled' => 1],
            ['id' => 6, 'name' => 'OPTIONS', 'enabled' => 1],
            ['id' => 7, 'name' => 'TRACE', 'enabled' => 1],
            ['id' => 8, 'name' => 'CONNECT', 'enabled' => 1],
        ]);
    }
}
