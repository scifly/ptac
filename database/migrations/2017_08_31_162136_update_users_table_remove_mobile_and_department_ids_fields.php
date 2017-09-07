<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateUsersTableRemoveMobileAndDepartmentIdsFields extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('department_ids');
            $table->dropColumn('mobile');
        });
    }
    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('users', function (Blueprint $table) {
            $table->string('department_ids')->nullable()->comment('所属部门IDs');
            $table->char('mobile', 11)->comment('手机号码');
        });
    }
}
