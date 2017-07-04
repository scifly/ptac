<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAttachmentTypesTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        
        if (!Schema::hasTable('attachment_types')) {
            Schema::create('attachment_types', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name', 60)->comment('附件类型名称');
                $table->string('remark')->comment('附件类型备注');
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
        
        Schema::dropIfExists('attachment_types');
        
    }
    
}
