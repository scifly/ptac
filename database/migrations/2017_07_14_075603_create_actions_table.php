<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActionsTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        
        if (!Schema::hasTable('actions')) {
            Schema::create('actions', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name')->comment('功能名称');
                $table->string('method')->comment('方法名称');
                $table->string('remark')->comment('备注');
                $table->string('controller')->comment('所属controller类名');
                $table->string('view')->nullable()->comment('对应的blade view名');
                $table->string('route')->nullable()->comment('对应的route名称');
                $table->string('js')->nullable()->comment('定制JS脚本路径');
                $table->boolean('datatable')->comment('是否使用datatable插件');
                $table->boolean('parsley')->comment('是否使用parsley插件');
                $table->boolean('select2')->comment('是否使用select2插件');
                $table->boolean('gritter')->comment('是否使用gritter插件');
                $table->timestamps();
                $table->boolean('enabled');
            });
        }
        
    }
    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        
        Schema::dropIfExists('actions');
        
    }
    
}
