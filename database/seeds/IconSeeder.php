<?php

use Illuminate\Database\Seeder;

class IconSeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        DB::table('icons')->insert([
            ['id' => 1, 'name' => 'fa fa-commenting', 'icon_type_id' => 1, 'enabled' => 1],
            ['id' => 2, 'name' => 'fa fa-phone-square', 'icon_type_id' => 1, 'enabled' => 1],
            ['id' => 3, 'name' => 'fa fa-photo', 'icon_type_id' => 1, 'enabled' => 1],
            ['id' => 4, 'name' => 'fa fa-calculator', 'icon_type_id' => 1, 'enabled' => 1],
            ['id' => 5, 'name' => 'fa fa-clock-o', 'icon_type_id' => 1, 'enabled' => 1],
            ['id' => 6, 'name' => 'fa fa-credit-card', 'icon_type_id' => 1, 'enabled' => 1],
            ['id' => 7, 'name' => 'fa fa-envelope', 'icon_type_id' => 1, 'enabled' => 1],
            ['id' => 8, 'name' => 'fa fa-folder', 'icon_type_id' => 1, 'enabled' => 1],
            ['id' => 9, 'name' => 'fa fa-folder-open', 'icon_type_id' => 1, 'enabled' => 1],
            ['id' => 10, 'name' => 'fa fa-user', 'icon_type_id' => 1, 'enabled' => 1],
            ['id' => 11, 'name' => 'fa fa-users', 'icon_type_id' => 1, 'enabled' => 1],
            ['id' => 12, 'name' => 'fa fa-user-plus', 'icon_type_id' => 1, 'enabled' => 1],
            ['id' => 13, 'name' => 'fa fa-trash', 'icon_type_id' => 1, 'enabled' => 1],
            ['id' => 14, 'name' => 'fa fa-file', 'icon_type_id' => 1, 'enabled' => 1],
            ['id' => 15, 'name' => 'fa fa-cog', 'icon_type_id' => 1, 'enabled' => 1],
            ['id' => 16, 'name' => 'fa fa-youtube-play', 'icon_type_id' => 1, 'enabled' => 1],
            ['id' => 17, 'name' => 'fa fa-weixin', 'icon_type_id' => 1, 'enabled' => 1],
            ['id' => 18, 'name' => 'fa fa-heart', 'icon_type_id' => 1, 'enabled' => 1],
            ['id' => 19, 'name' => 'fa fa-exclamation-circle', 'icon_type_id' => 1, 'enabled' => 1],
            ['id' => 20, 'name' => 'fa fa-book', 'icon_type_id' => 1, 'enabled' => 1],
            ['id' => 21, 'name' => 'fa fa-picture-o', 'icon_type_id' => 1, 'enabled' => 1],
            ['id' => 22, 'name' => 'fa fa-plus', 'icon_type_id' => 1, 'enabled' => 1],
            ['id' => 23, 'name' => 'fa fa-minus', 'icon_type_id' => 1, 'enabled' => 1],
            ['id' => 24, 'name' => 'fa fa-print', 'icon_type_id' => 1, 'enabled' => 1],
            ['id' => 25, 'name' => 'fa fa-remove', 'icon_type_id' => 1, 'enabled' => 1],
            ['id' => 26, 'name' => 'fa fa-search', 'icon_type_id' => 1, 'enabled' => 1],
            ['id' => 27, 'name' => 'fa fa-bell-slash', 'icon_type_id' => 1, 'enabled' => 1],
            ['id' => 28, 'name' => 'fa fa-bell', 'icon_type_id' => 1, 'enabled' => 1],
            ['id' => 29, 'name' => 'fa fa-download', 'icon_type_id' => 1, 'enabled' => 1],
            ['id' => 30, 'name' => 'fa fa-upload', 'icon_type_id' => 1, 'enabled' => 1]
        ]);
    }
}
