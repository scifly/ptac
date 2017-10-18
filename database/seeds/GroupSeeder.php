<?php

use Illuminate\Database\Seeder;

class GroupSeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        DB::table('groups')->insert([
            ['id' => 1, 'name' => 'root', 'remark' => '超级用户', 'enabled' => 1],
            ['id' => 2, 'name' => 'operator', 'remark' => '管理员', 'enabled' => 1],
            ['id' => 3, 'name' => 'user', 'remark' => '普通用户', 'enabled' => 1],
            ['id' => 4, 'name' => 'custodian', 'remark' => '监护人', 'enabled' => 1],
            ['id' => 5, 'name' => 'educator', 'remark' => '教职员工', 'enabled' => 1],
            ['id' => 6, 'name' => 'student', 'remark' => '学生', 'enabled' => 1],
        ]);
    }
}
